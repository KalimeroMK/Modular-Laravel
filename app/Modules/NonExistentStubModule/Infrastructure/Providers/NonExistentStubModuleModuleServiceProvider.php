<?php

declare(strict_types=1);

namespace App\Modules\NonExistentStubModule\Infrastructure\Providers;

use App\Modules\NonExistentStubModule\Infrastructure\Repositories\NonExistentStubModuleRepository;
use App\Modules\NonExistentStubModule\Infrastructure\Repositories\NonExistentStubModuleRepositoryInterface;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class NonExistentStubModuleModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NonExistentStubModuleRepositoryInterface::class, NonExistentStubModuleRepository::class);
    }

    public function boot(): void
    {
        $this->registerPolicies();
        $this->registerObservers();
        $this->registerEvents();
        $this->loadRoutes();
    }

    protected function loadRoutes(): void
    {
        $routeFile = __DIR__.'/../Routes/nonexistentstubmodule.php';

        if (file_exists($routeFile)) {
            Route::group([], function () use ($routeFile) {
                require $routeFile;
            });
        }
    }

    protected function registerPolicies(): void
    {
        $policyClass = 'App\\Modules\\NonExistentStubModule\\Infrastructure\\Policies\\NonExistentStubModulePolicy';

        if (class_exists($policyClass) && class_exists(NonExistentStubModule::class)) {
            Gate::policy(NonExistentStubModule::class, $policyClass);
        }
    }

    protected function registerObservers(): void
    {
        $observerClass = 'App\\Modules\\NonExistentStubModule\\Infrastructure\\Observers\\NonExistentStubModuleObserver';

        if (class_exists($observerClass) && class_exists(NonExistentStubModule::class)) {
            NonExistentStubModule::observe($observerClass);
        }
    }

    protected function registerEvents(): void
    {
        $basePath = app_path('Modules/NonExistentStubModule');
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
