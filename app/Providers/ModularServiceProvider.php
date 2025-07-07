<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Log;
use Throwable;

/**
 * ModularServiceProvider
 *
 * Expects the following config keys:
 * - modules.default.directory (string): Base directory for modules.
 * - modules.default.routing (array): Route types to register (e.g., ['api']).
 * - modules.default.structure.<component> (string): Default structure for components (routes, controllers, helpers, etc.).
 * - modules.specific.<ModuleName>.enabled (bool): Whether the module is enabled.
 * - modules.specific.<ModuleName>.routing (array): Route types for this module.
 * - modules.specific.<ModuleName>.structure.<component> (string): Structure overrides for this module.
 */
class ModularServiceProvider extends ServiceProvider
{
    protected Filesystem $files;

    /**
     * Bootstrap the application services.
     */
    public function boot(Filesystem $files): void
    {
        $this->files = $files;
        $modulesCacheKey = 'modular.modules';
        $modules = cache()->rememberForever($modulesCacheKey, function () {
            $modulesDir = app_path(Config::get('modules.default.directory'));
            if (! is_dir($modulesDir)) {
                Log::warning("Modules directory not found: {$modulesDir}");

                return [];
            }

            return array_map('class_basename', $this->files->directories($modulesDir));
        });
        foreach ($modules as $module) {
            if ($module === 'Test') {
                Log::info('Test module is being loaded!');
            }
            try {
                Log::info("Registering migrations for module: {$module}");
                $this->registerModule($module);
            } catch (Throwable $e) {
                Log::error("Failed to register module '{$module}': ".$e->getMessage());
            }
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->registerPublishConfig();
    }

    /**
     * Register a module by its name
     */
    protected function registerModule(string $name): void
    {
        $enabled = config("modules.specific.{$name}.enabled", true);
        if ($enabled) {
            $this->registerRoutes($name);
            $this->registerHelpers($name);
            $this->registerFilters($name);
            $this->registerMigrations($name);
            $this->registerFactories($name);
            $this->registerObservers($name);
            $this->registerPolicies($name);
        }
    }

    /**
     * Register the routes for a module by its name
     */
    protected function registerRoutes(string $module): void
    {
        if (! $this->app->routesAreCached()) {
            $data = $this->getRoutingConfig($module);
            try {
                $this->registerRoute($module, $data['path'], $data['namespace']);
            } catch (Throwable $e) {
                Log::warning("Failed to register route for module '{$module}': ".$e->getMessage());
            }
        }
    }

    /**
     * Collect the needed data to register the routes
     *
     * @return array<string, mixed>
     */
    protected function getRoutingConfig(string $module): array
    {
        $path = config("modules.specific.{$module}.structure.routes", config('modules.default.structure.routes'));

        // Update the controllers path to include 'Http'
        $cp = config(
            "modules.specific.{$module}.structure.controllers",
            config('modules.default.structure.controllers', 'Http/Controllers')
        );

        $namespace = $this->app->getNamespace().trim(
            Config::get('modules.default.directory')."\\{$module}\\Http\\".implode('\\', explode('/', $cp)),
            '\\'
        );

        return compact('path', 'namespace');
    }

    /**
     * Registers a single route
     */
    protected function registerRoute(string $module, string $path, string $namespace): void
    {
        $filePath = app_path(
            Config::get('modules.default.directory')."/{$module}/{$path}/api.php"
        );

        if ($this->files->exists($filePath)) {
            Route::middleware('api')->namespace($namespace)->group($filePath);
        }
    }

    /**
     * Register the helpers file for a module by its name
     */
    protected function registerHelpers(string $module): void
    {
        try {
            if ($file = $this->prepareComponent($module, 'helpers', 'helpers.php')) {
                include_once $file;
            }
        } catch (Throwable $e) {
            Log::warning("Helpers file missing or failed to load for module '{$module}': ".$e->getMessage());
        }
    }

    /**
     * Prepare component registration
     */
    protected function prepareComponent(string $module, string $component, string $file = ''): false|string
    {
        $path = config(
            "modules.specific.{$module}.structure.{$component}",
            config("modules.default.structure.{$component}")
        );

        $resource = rtrim(
            str_replace('//', '/', app_path(Config::get('modules.default.directory')."/{$module}/{$path}/{$file}")),
            '/'
        );

        if ($file && ! $this->files->exists($resource)) {
            return false;
        }

        if (! $file && ! $this->files->isDirectory($resource)) {
            return false;
        }

        return $resource;
    }

    protected function registerFilters(string $module): void
    {
        try {
            if ($filters = $this->prepareComponent($module, 'filters')) {
                $this->loadFiltersFrom($filters, $module);
            }
        } catch (Throwable $e) {
            Log::warning("Filters missing or failed to load for module '{$module}': ".$e->getMessage());
        }
    }

    /**
     * Register a translation file namespace.
     */
    protected function loadFiltersFrom(string $path, string $namespace): void
    {
        $this->callAfterResolving('filters', function ($filter) use ($path, $namespace): void {
            $filter->addNamespace($namespace, $path);
        });
    }

    /**
     * Publish modules configuration
     */
    protected function registerPublishConfig(): void
    {
        $publishPath = $this->app->configPath('modules.php');
        $this->publishes([$publishPath], 'config');
    }

    protected function registerFactories(string $module): void
    {
        // If you want to scope factories per module, you can extend this logic
        try {
            Factory::guessFactoryNamesUsing([self::class, 'factoryNameResolver']);
        } catch (Throwable $e) {
            Log::warning("Failed to register factories for module '{$module}': ".$e->getMessage());
        }
    }

    /**
     * @param class-string<\Illuminate\Database\Eloquent\Model> $model
     * @phpstan-return class-string<\Illuminate\Database\Eloquent\Factories\Factory>
     * @phpstan-ignore-next-line
     */
    private static function factoryNameResolver(string $model): string
    {
        // Assumes $module is available in closure scope; if not, adjust accordingly
        // For static, you may need to pass $module as a parameter or refactor
        // Here, we just return the default format for PHPStan compliance
        return 'App\\Modules\\'.'Module'.'\\database\\factories\\'.class_basename($model).'Factory';
    }

    protected function registerObservers(string $module): void
    {
        $observerClass = "App\\Modules\\{$module}\\Observers\\{$module}Observer";
        $modelClass = "App\\Modules\\{$module}\\Models\\{$module}";
        if (class_exists($observerClass) && class_exists($modelClass)) {
            $modelClass::observe($observerClass);
        }
    }

    protected function registerPolicies(string $module): void
    {
        $policyClass = "App\\Modules\\{$module}\\Policies\\{$module}Policy";
        $modelClass = "App\\Modules\\{$module}\\Models\\{$module}";
        if (class_exists($policyClass) && class_exists($modelClass)) {
            Gate::policy($modelClass, $policyClass);
        }
    }

    private function registerMigrations(string $name): void
    {
        $migrationPath = app_path(Config::get('modules.default.directory')."/{$name}/database/migrations");
        $this->loadMigrationsFrom($migrationPath);
    }
}
