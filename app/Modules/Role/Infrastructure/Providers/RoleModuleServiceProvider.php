<?php

declare(strict_types=1);

namespace App\Modules\Role\Infrastructure\Providers;

use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\Role\Infrastructure\Policies\RolePolicy;
use App\Modules\Role\Infrastructure\Repositories\RoleRepository;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Override;

class RoleModuleServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
    }

    public function boot(): void
    {
        
        if (! $this->isModuleEnabled()) {
            return;
        }

        $this->registerPolicies();
        $this->loadRoutes();
    }

    


    protected function isModuleEnabled(): bool
    {
        return (bool) config('modules.specific.Role.enabled', true);
    }

    protected function registerPolicies(): void
    {
        Gate::policy(Role::class, RolePolicy::class);
    }

    protected function loadRoutes(): void
    {
        $routeFile = __DIR__.'/../Routes/roles.php';

        if (! file_exists($routeFile)) {
            return;
        }

        
        require $routeFile;
    }
}
