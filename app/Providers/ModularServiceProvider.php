<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Throwable;

class ModularServiceProvider extends ServiceProvider
{
    protected Filesystem $files;

    public function boot(Filesystem $files): void
    {
        $this->files = $files;

        // Read base path & namespace from config
        $basePath = rtrim((string) config('modules.default.base_path', base_path('app/Modules')), '/');
        $nsBase = rtrim((string) config('modules.default.namespace', 'App\\Modules'), '\\');

        if (! is_dir($basePath)) {
            Log::warning("Modules base path not found: {$basePath}");

            return;
        }

        // Cache modules list (short TTL in dev, forever in prod)
        $cacheKey = 'modular.modules.list';
        $ttl = app()->environment('production') ? null : now()->addMinutes(5);

        $modules = Cache::remember($cacheKey, $ttl, function () use ($basePath) {
            $dirs = array_filter(scandir($basePath) ?: [], function ($d) use ($basePath) {
                return $d !== '.' && $d !== '..' && is_dir("{$basePath}/{$d}");
            });

            return array_values($dirs);
        });

        foreach ($modules as $module) {
            try {
                if (! $this->isModuleEnabled($module)) {
                    continue;
                }

                $this->registerRoutes($module, $basePath, $nsBase);
                $this->registerHelpers($module, $basePath);
                $this->registerMigrations($module, $basePath);
                $this->registerFactoriesResolver($basePath, $nsBase);
                $this->registerObservers($module, $nsBase);
                $this->registerPolicies($module, $nsBase);
            } catch (Throwable $e) {
                Log::error("Failed to register module '{$module}': ".$e->getMessage());
            }
        }
    }

    public function register(): void {}

    protected function isModuleEnabled(string $module): bool
    {
        return (bool) config("modules.specific.{$module}.enabled", true);
    }

    /**
     * API-only route registration with prefix/version/middleware from config.
     */
    protected function registerRoutes(string $module, string $basePath, string $nsBase): void
    {
        // Route caching check removed for Laravel 12 compatibility

        $structure = config('modules.default.structure', []);
        $routesDir = $structure['routes'] ?? 'routes';
        $routeFile = "{$basePath}/{$module}/{$routesDir}/api.php";

        if (! is_file($routeFile)) {
            return;
        }

        $opt = config('modules.default.routing_options.api', [
            'prefix' => 'api',
            'version' => 'v1',
            'middleware' => ['api'],
        ]);

        // Build controller namespace from config structure (no extra "Http" duplication)
        $controllersRel = $structure['controllers'] ?? 'Http/Controllers';
        $controllersNS = str_replace('/', '\\', $controllersRel);

        $nsControllers = "{$nsBase}\\{$module}\\{$controllersNS}";

        Route::group(array_filter([
            'middleware' => $opt['middleware'] ?? ['api'],
            'namespace' => $nsControllers, // Optional if you use FQCN in routes; keep for convenience
        ]), static function () use ($routeFile) {
            require $routeFile;
        });
    }

    /**
     * Include module helpers if present.
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
     * Factory resolver: App\Modules\X\Infrastructure\Models\Post -> App\Modules\X\Database\Factories\PostFactory
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

    /**
     * Naive observer auto-wire (optional – consider config or attributes for production).
     */
    protected function registerObservers(string $module, string $nsBase): void
    {
        $structure = config('modules.default.structure', []);
        $observersRel = $structure['observers'] ?? 'Observers';
        $modelsRel = $structure['models'] ?? 'Infrastructure/Models';

        $observersNs = "{$nsBase}\\{$module}\\".str_replace('/', '\\', $observersRel);
        $modelsNs = "{$nsBase}\\{$module}\\".str_replace('/', '\\', $modelsRel);

        $basePath = rtrim((string) config('modules.default.base_path', base_path('app/Modules')), '/');
        $observersDir = "{$basePath}/{$module}/{$observersRel}";

        if (! is_dir($observersDir)) {
            return;
        }

        $fs = $this->files ?? new Filesystem();

        foreach ($fs->files($observersDir) as $file) {
            $name = $file->getFilename();               // e.g. PostObserver.php
            if (! str_ends_with($name, 'Observer.php')) {
                continue;
            }

            $observerClass = pathinfo($name, PATHINFO_FILENAME); // PostObserver
            $modelClass = mb_substr($observerClass, 0, -8);      // Post

            $observerFqcn = "{$observersNs}\\{$observerClass}";
            $modelFqcn = "{$modelsNs}\\{$modelClass}";

            if (class_exists($observerFqcn) && class_exists($modelFqcn)) {
                /** @var class-string<Model> $modelFqcn */
                $modelFqcn::observe($observerFqcn);
            }
        }
    }

    /**
     * Naive policy registration – prefer per-module config or auto-discovery.
     */
    protected function registerPolicies(string $module, string $nsBase): void
    {
        $policy = "{$nsBase}\\{$module}\\Infrastructure\\Policies\\{$module}Policy";
        $model = "{$nsBase}\\{$module}\\Infrastructure\\Models\\{$module}";

        if (class_exists($policy) && class_exists($model)) {
            Gate::policy($model, $policy);
        }
    }
}
