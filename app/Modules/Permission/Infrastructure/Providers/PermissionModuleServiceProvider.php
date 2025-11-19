<?php

declare(strict_types=1);

namespace App\Modules\Permission\Infrastructure\Providers;

use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepository;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PermissionModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind interfaces to implementations
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
    }

    public function boot(): void
    {
        // Register route model binding BEFORE loading routes
        Route::bind('permission', function ($value) {
            return Permission::findOrFail($value);
        });

        // Load routes
        $this->loadRoutes();
    }

    protected function loadRoutes(): void
    {
        $routeFile = __DIR__.'/../Routes/permissions.php';

        if (file_exists($routeFile)) {
            Route::group([], function () use ($routeFile) {
                require $routeFile;
            });
        }
    }
}
