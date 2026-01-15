<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Override;
use Throwable;

/**
 * ModularServiceProvider handles GLOBAL resources for all modules:
 * - Factory resolver (global, registered once)
 * - Migrations (module-specific, but registered globally)
 * - Helpers (module-specific, but registered globally)
 *
 * Module-specific resources (routes, policies, observers, events, repository bindings)
 * are handled by individual module service providers.
 */
class ModularServiceProvider extends ServiceProvider
{
    protected Filesystem $files;

    public function boot(Filesystem $files): void
    {
        $this->files = $files;

        // Read base path & namespace from config
        $basePath = mb_rtrim((string) config('modules.default.base_path', base_path('app/Modules')), '/');
        $nsBase = mb_rtrim((string) config('modules.default.namespace', 'App\\Modules'), '\\');

        if (! is_dir($basePath)) {
            Log::warning("Modules base path not found: {$basePath}");

            return;
        }

        // Cache modules list (short TTL in dev, forever in prod)
        $cacheKey = 'modular.modules.list';
        $ttl = app()->environment('production') ? null : now()->addMinutes(5);

        $modules = Cache::remember($cacheKey, $ttl, function () use ($basePath) {
            $dirs = array_filter(scandir($basePath) ?: [], fn ($d) => $d !== '.'
                && $d !== '..'
                && is_dir("{$basePath}/{$d}")
                && ! str_starts_with($d, 'NonExistent')
                && ! str_starts_with($d, 'Test')
                && ! str_contains($d, 'Test'));

            return array_values($dirs);
        });

        // Register global factory resolver (once for all modules)
        $this->registerFactoriesResolver($basePath, $nsBase);

        // Register module-specific resources (migrations, helpers)
        // Routes, policies, observers, and events are registered by individual module service providers
        foreach ($modules as $module) {
            try {
                $this->registerHelpers($module, $basePath);
                $this->registerMigrations($module, $basePath);
            } catch (Throwable $e) {
                Log::error("Failed to register module '{$module}': ".$e->getMessage());
            }
        }
    }

    #[Override]
    public function register(): void {}

    /**
     * Include module helpers if present.
     * This is module-specific but registered globally for convenience.
     */
    protected function registerHelpers(string $module, string $basePath): void
    {
        $structure = config('modules.default.structure', []);
        $helpersDir = $structure['support'] ?? ($structure['helpers'] ?? '');
        if (! $helpersDir) {
            return;
        }

        $helpersFile = "{$basePath}/{$module}/{$helpersDir}/helpers.php";
        if (is_file($helpersFile)) {
            try {
                include_once $helpersFile;
            } catch (Throwable $e) {
                Log::warning("Helpers failed to load for module '{$module}': ".$e->getMessage());
            }
        }
    }

    /**
     * Register module migrations (safe if path doesn't exist).
     * This is module-specific but registered globally for convenience.
     */
    protected function registerMigrations(string $module, string $basePath): void
    {
        $structure = config('modules.default.structure', []);
        $migrRel = $structure['migrations'] ?? 'database/migrations';
        $migrPath = "{$basePath}/{$module}/{$migrRel}";

        if (is_dir($migrPath)) {
            $this->loadMigrationsFrom($migrPath);
        }
    }

    /**
     * Register global factory resolver for all modules.
     * Maps: App\Modules\X\Infrastructure\Models\Post -> App\Modules\X\Database\Factories\PostFactory
     * This is a GLOBAL resource, registered once for all modules.
     */
    protected function registerFactoriesResolver(string $basePath, string $nsBase): void
    {
        Factory::guessFactoryNamesUsing(static function (string $modelFqcn): string {
            // Replace "\Infrastructure\Models\" with "\Database\Factories\" (PSR-4 aligned)
            $factoryFqcn = str_replace(
                ['\\Infrastructure\\Models\\', '\\Models\\', '\\Model\\'],
                '\\Database\\Factories\\',
                $modelFqcn
            );

            /** @var class-string<Factory<Model>> $factoryClass */
            $factoryClass = $factoryFqcn.'Factory';

            return $factoryClass;
        });
    }
}
