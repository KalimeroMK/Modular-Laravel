<?php

declare(strict_types=1);

namespace App\Modules\Permission\Infrastructure\Providers;

use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Permission\Infrastructure\Policies\PermissionPolicy;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepository;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Override;

class PermissionModuleServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        // Bind interfaces to implementations
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
    }

    public function boot(): void
    {
        // Check if module is enabled before loading
        if (! $this->isModuleEnabled()) {
            return;
        }

        $this->registerPolicies();
        $this->loadRoutes();
    }

    /**
     * Check if this module is enabled in config/modules.php
     */
    protected function isModuleEnabled(): bool
    {
        return (bool) config('modules.specific.Permission.enabled', true);
    }

    protected function registerPolicies(): void
    {
        Gate::policy(Permission::class, PermissionPolicy::class);
    }

    protected function loadRoutes(): void
    {
        $routeFile = __DIR__.'/../Routes/permissions.php';

        if (! file_exists($routeFile)) {
            return;
        }

        // Routes already have prefix and middleware in route files, just require them
        require $routeFile;
    }
}
