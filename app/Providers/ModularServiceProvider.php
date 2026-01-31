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










class ModularServiceProvider extends ServiceProvider
{
    protected Filesystem $files;

    public function boot(Filesystem $files): void
    {
        $this->files = $files;

        
        $basePath = mb_rtrim((string) config('modules.default.base_path', base_path('app/Modules')), '/');
        $nsBase = mb_rtrim((string) config('modules.default.namespace', 'App\\Modules'), '\\');

        if (! is_dir($basePath)) {
            Log::warning("Modules base path not found: {$basePath}");

            return;
        }

        
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

        
        $this->registerFactoriesResolver($basePath, $nsBase);

        
        
        foreach ($modules as $module) {
            try {
                $this->registerHelpers($module, $basePath);
                $this->registerMigrations($module, $basePath);
            } catch (Throwable $e) {
                Log::error("Failed to register module '{$module}': ".$e->getMessage());
            }
        }
    }

    
    public function register(): void {}

    



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

    



    protected function registerMigrations(string $module, string $basePath): void
    {
        $structure = config('modules.default.structure', []);
        $migrRel = $structure['migrations'] ?? 'database/migrations';
        $migrPath = "{$basePath}/{$module}/{$migrRel}";

        if (is_dir($migrPath)) {
            $this->loadMigrationsFrom($migrPath);
        }
    }

    




    protected function registerFactoriesResolver(string $basePath, string $nsBase): void
    {
        Factory::guessFactoryNamesUsing(static function (string $modelFqcn): string {
            
            $factoryFqcn = str_replace(
                ['\\Infrastructure\\Models\\', '\\Models\\', '\\Model\\'],
                '\\Database\\Factories\\',
                $modelFqcn
            );

             
            $factoryClass = $factoryFqcn.'Factory';

            return $factoryClass;
        });
    }
}
