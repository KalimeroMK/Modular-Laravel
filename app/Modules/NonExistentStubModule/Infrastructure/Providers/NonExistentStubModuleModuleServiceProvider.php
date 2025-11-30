<?php

declare(strict_types=1);

namespace App\Modules\NonExistentStubModule\Infrastructure\Providers;

use App\Modules\NonExistentStubModule\Infrastructure\Repositories\NonExistentStubModuleRepository;
use App\Modules\NonExistentStubModule\Infrastructure\Repositories\NonExistentStubModuleRepositoryInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class NonExistentStubModuleModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind repository interface to implementation
        $this->app->bind(NonExistentStubModuleRepositoryInterface::class, NonExistentStubModuleRepository::class);
    }

    public function boot(): void
    {
        // Load routes
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
}

