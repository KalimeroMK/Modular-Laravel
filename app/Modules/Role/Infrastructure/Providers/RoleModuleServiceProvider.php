<?php

declare(strict_types=1);

namespace App\Modules\Role\Infrastructure\Providers;

use App\Modules\Role\Infrastructure\Repositories\RoleRepository;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RoleModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind interfaces to implementations
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
    }

    public function boot(): void
    {
        // Register route model binding BEFORE loading routes
        Route::bind('role', function ($value) {
            return \App\Modules\Role\Infrastructure\Models\Role::findOrFail($value);
        });
        
        // Load routes
        $this->loadRoutes();
    }

    protected function loadRoutes(): void
    {
        $routeFile = __DIR__.'/../Routes/roles.php';

        if (file_exists($routeFile)) {
            Route::group([], function () use ($routeFile) {
                require $routeFile;
            });
        }
    }
}
