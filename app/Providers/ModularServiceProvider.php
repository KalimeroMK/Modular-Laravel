<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class ModularServiceProvider extends ServiceProvider
{
    protected $files;

    /**
     * Bootstrap the application services.
     */
    public function boot(Filesystem $files): void
    {
        $this->files = $files;
        if (is_dir(app_path(Config::get('modules.default.directory')))) {
            $modules = array_map(
                'class_basename',
                $this->files->directories(app_path(Config::get('modules.default.directory')))
            );
            foreach ($modules as $module) {
                // Allow routes to be cached
                $this->registerModule($module);
            }
        }
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
            $this->registerViews($name);
            $this->registerTranslations($name);
            $this->registerFilters($name);
            $this->registerMigrations($name);
            $this->registerFactories($name);
        }
    }

    /**
     * Register the routes for a module by its name
     */
    protected function registerRoutes(string $module): void
    {
        if (! $this->app->routesAreCached()) {
            $data = $this->getRoutingConfig($module);

            foreach ($data['types'] as $type) {
                $this->registerRoute($module, $data['path'], $data['namespace'], $type);
            }
        }
    }

    /**
     * Collect the needed data to register the routes
     */
    protected function getRoutingConfig(string $module): array
    {
        $types = config("modules.specific.{$module}.routing", config('modules.default.routing'));
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

        return compact('types', 'path', 'namespace');
    }

    /**
     * Registers a single route
     */
    protected function registerRoute(string $module, string $path, string $namespace, string $type): void
    {
        if (in_array($type, ['web', 'api'])) {
            $filePath = app_path(
                Config::get('modules.default.directory')."/{$module}/{$path}/{$type}.php"
            );

            if ($this->files->exists($filePath)) {
                Route::middleware($type)->namespace($namespace)->group($filePath);
            }
        }
    }

    /**
     * Register the helpers file for a module by its name
     */
    protected function registerHelpers(string $module): void
    {
        if ($file = $this->prepareComponent($module, 'helpers', 'helpers.php')) {
            include_once $file;
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

    /**
     * Register the views for a module by its name
     */
    protected function registerViews(string $module): void
    {
        $viewsPath = config("modules.specific.{$module}.structure.views", config('modules.default.structure.views'));

        $moduleViewsPath = app_path(config('modules.default.directory')."/{$module}/{$viewsPath}");
        if ($this->files->isDirectory($moduleViewsPath)) {
            $this->loadViewsFrom($moduleViewsPath, Str::kebab($module));
        }
    }

    /**
     * Register the translations for a module by its name
     */
    protected function registerTranslations(string $module): void
    {
        if ($translations = $this->prepareComponent($module, 'translations')) {
            $this->loadTranslationsFrom($translations, $module);
        }
    }

    protected function registerFilters(string $module): void
    {
        if ($filters = $this->prepareComponent($module, 'filters')) {
            $this->loadFiltersFrom($filters, $module);
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
     * Register the application services.
     */
    public function register(): void
    {
        $this->registerPublishConfig();
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
        Factory::guessFactoryNamesUsing(function (string $module) {
            return 'Database\\Factories\\'.class_basename($module).'Factory';
        });
    }

    private function registerMigrations(string $name): void
    {
        $this->loadMigrationsFrom(app_path(Config::get('modules.default.directory')."/{$name}/database/migrations"));
    }
}
