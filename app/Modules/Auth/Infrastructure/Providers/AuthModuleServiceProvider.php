<?php

declare(strict_types=1);

namespace App\Modules\Auth\Infrastructure\Providers;

use App\Modules\Auth\Application\Services\IssueTokenService;
use App\Modules\Auth\Application\Services\IssueTokenServiceInterface;
use App\Modules\Auth\Application\Services\TwoFactor\Service;
use App\Modules\Auth\Application\Services\TwoFactor\ServiceInterface;
use App\Modules\Auth\Infrastructure\Repositories\AuthRepository;
use App\Modules\Auth\Infrastructure\Repositories\AuthRepositoryInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use PragmaRX\Google2FA\Google2FA;

class AuthModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind interfaces to implementations
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(IssueTokenServiceInterface::class, IssueTokenService::class);

        // Bind 2FA service
        $this->app->bind(ServiceInterface::class, Service::class);
        $this->app->singleton(Google2FA::class, function () {
            return new Google2FA();
        });
    }

    public function boot(): void
    {
        // Load routes
        $this->loadRoutes();
    }

    protected function loadRoutes(): void
    {
        $routeFile = __DIR__.'/../Routes/auth.php';

        if (file_exists($routeFile)) {
            Route::group([], function () use ($routeFile) {
                require $routeFile;
            });
        }
    }
}
