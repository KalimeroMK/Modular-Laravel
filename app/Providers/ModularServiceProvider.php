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
        $basePath = rtrim((string) config('modules.default.base_path', base_path('Modules')), '/');
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

    public function register(): void
    {
        // No-op for app provider. If this becomes a package, wire publishes here.
    }

    protected function isModuleEnabled(string $module): bool
    {
        return (bool) config("modules.specific.{$module}.enabled", true);
    }

    /**
     * API-only route registration with prefix/version/middleware from config.
     */
    protected function registerRoutes(string $module, string $basePath, string $nsBase): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

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
            'prefix' => trim(($opt['prefix'] ?? 'api').'/'.($opt['version'] ?? ''), '/'),
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
     * Factory resolver: App\Modules\X\Models\Post -> App\Modules\X\database\factories\PostFactory
     */
    protected function registerFactoriesResolver(string $basePath, string $nsBase): void
    {
        Factory::guessFactoryNamesUsing(static function (string $modelFqcn): string {
            // Replace "\Models\" with "\database\factories\" (PSR-4 aligned)
            $factoryFqcn = str_replace(
                ['\\Models\\', '\\Model\\'],
                '\\database\\factories\\',
                $modelFqcn
            );

            return $factoryFqcn.'Factory';
        });
    }

    /**
     * Naive observer auto-wire (optional – consider config or attributes for production).
     */
    protected function registerObservers(string $module, string $nsBase): void
    {
        $structure = config('modules.default.structure', []);
        $observersRel = $structure['observers'] ?? 'Observers';
        $modelsRel = $structure['models'] ?? 'Models';

        $observersNs = "{$nsBase}\\{$module}\\".str_replace('/', '\\', $observersRel);
        $modelsNs = "{$nsBase}\\{$module}\\".str_replace('/', '\\', $modelsRel);

        $basePath = rtrim((string) config('modules.default.base_path', base_path('Modules')), '/');
        $observersDir = "{$basePath}/{$module}/{$observersRel}";

        if (! is_dir($observersDir)) {
            return;
        }

        /** @var Filesystem $fs */
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
        $policy = "{$nsBase}\\{$module}\\Policies\\{$module}Policy";
        $model = "{$nsBase}\\{$module}\\Models\\{$module}";

        if (class_exists($policy) && class_exists($model)) {
            Gate::policy($model, $policy);
        }
    }
}
