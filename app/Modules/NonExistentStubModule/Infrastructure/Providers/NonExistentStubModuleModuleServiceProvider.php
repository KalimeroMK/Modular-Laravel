<?php

declare(strict_types=1);

namespace App\Modules\NonExistentStubModule\Infrastructure\Providers;

use App\Modules\NonExistentStubModule\Infrastructure\Models\NonExistentStubModule;
use App\Modules\NonExistentStubModule\Infrastructure\Repositories\NonExistentStubModuleRepository;
use App\Modules\NonExistentStubModule\Infrastructure\Repositories\NonExistentStubModuleRepositoryInterface;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class NonExistentStubModuleModuleServiceProvider extends ServiceProvider
{
    /**
     * Register module-specific bindings (repositories, services, etc.)
     * This is module-specific and should be in individual service providers.
     */
    public function register(): void
    {
        $this->app->bind(NonExistentStubModuleRepositoryInterface::class, NonExistentStubModuleRepository::class);
    }

    /**
     * Bootstrap module-specific resources (routes, policies, observers, events)
     * This is module-specific and should be in individual service providers.
     * ModularServiceProvider handles only global resources (migrations, factories, helpers).
     */
    public function boot(): void
    {
        // Check if module is enabled before loading
        if (! $this->isModuleEnabled()) {
            return;
        }

        $this->registerPolicies();
        $this->registerObservers();
        $this->registerEvents();
        $this->loadRoutes();
    }

    /**
     * Check if this module is enabled in config/modules.php
     */
    protected function isModuleEnabled(): bool
    {
        return (bool) config("modules.specific.NonExistentStubModule.enabled", true);
    }

    /**
     * Load module routes with prefix and middleware from config.
     * Each module is responsible for loading its own routes.
     */
    protected function loadRoutes(): void
    {
        $routeFile = __DIR__.'/../Routes/nonexistentstubmodule.php';

        if (! file_exists($routeFile)) {
            return;
        }

        $routingOptions = config('modules.default.routing_options.api', [
            'prefix' => 'api',
            'version' => 'v1',
            'middleware' => ['api'],
        ]);

        $prefix = ($routingOptions['prefix'] ?? 'api').'/'.($routingOptions['version'] ?? 'v1');
        $middleware = $routingOptions['middleware'] ?? ['api'];

        Route::group([
            'prefix' => $prefix,
            'middleware' => $middleware,
        ], function () use ($routeFile): void {
            require $routeFile;
        });
    }

    /**
     * Register module policies.
     * Each module is responsible for registering its own policies.
     */
    protected function registerPolicies(): void
    {
        $policyClass = "App\\Modules\\NonExistentStubModule\\Infrastructure\\Policies\\NonExistentStubModulePolicy";

        if (class_exists($policyClass) && class_exists(NonExistentStubModule::class)) {
            Gate::policy(NonExistentStubModule::class, $policyClass);
        }
    }

    /**
     * Register module observers.
     * Each module is responsible for registering its own observers.
     */
    protected function registerObservers(): void
    {
        $observerClass = "App\\Modules\\NonExistentStubModule\\Infrastructure\\Observers\\NonExistentStubModuleObserver";

        if (class_exists($observerClass) && class_exists(NonExistentStubModule::class)) {
            NonExistentStubModule::observe($observerClass);
        }
    }

    /**
     * Register module events and listeners.
     * Each module is responsible for registering its own events.
     */
    protected function registerEvents(): void
    {
        $basePath = app_path("Modules/NonExistentStubModule");
        $eventsPath = "{$basePath}/Application/Events";
        $listenersPath = "{$basePath}/Application/Listeners";

        if (! is_dir($eventsPath) || ! is_dir($listenersPath)) {
            return;
        }

        $fs = new Filesystem();

        foreach ($fs->files($eventsPath) as $eventFile) {
            $eventName = $eventFile->getFilenameWithoutExtension();
            $eventClass = "App\\Modules\\NonExistentStubModule\\Application\\Events\\{$eventName}";

            if (! class_exists($eventClass)) {
                continue;
            }

            $listenerName = $eventName.'Listener';
            $listenerClass = "App\\Modules\\NonExistentStubModule\\Application\\Listeners\\{$listenerName}";

            if (class_exists($listenerClass)) {
                Event::listen($eventClass, $listenerClass);
            }
        }
    }
}

