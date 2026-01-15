<?php

declare(strict_types=1);

namespace App\Modules\User\Infrastructure\Providers;

use App\Modules\User\Infrastructure\Models\User;
use App\Modules\User\Infrastructure\Policies\UserPolicy;
use App\Modules\User\Infrastructure\Repositories\UserRepository;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Override;

class UserModuleServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        // Bind interfaces to implementations
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    public function boot(): void
    {
        $this->registerPolicies();
        $this->loadRoutes();
    }

    protected function registerPolicies(): void
    {
        Gate::policy(User::class, UserPolicy::class);
    }

    protected function loadRoutes(): void
    {
        $routeFile = __DIR__.'/../Routes/users.php';

        if (file_exists($routeFile)) {
            Route::group([], function () use ($routeFile): void {
                require $routeFile;
            });
        }
    }
}
