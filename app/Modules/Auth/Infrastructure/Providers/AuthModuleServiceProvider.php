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
use Override;
use PragmaRX\Google2FA\Google2FA;

class AuthModuleServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        // Bind interfaces to implementations
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(IssueTokenServiceInterface::class, IssueTokenService::class);

        // Bind 2FA service
        $this->app->bind(ServiceInterface::class, Service::class);
        $this->app->singleton(Google2FA::class, fn () => new Google2FA());
    }

    public function boot(): void
    {
        // Check if module is enabled before loading
        if (! $this->isModuleEnabled()) {
            return;
        }

        // Load routes
        $this->loadRoutes();
    }

    /**
     * Check if this module is enabled in config/modules.php
     */
    protected function isModuleEnabled(): bool
    {
        return (bool) config('modules.specific.Auth.enabled', true);
    }

    protected function loadRoutes(): void
    {
        $routeFile = __DIR__.'/../Routes/auth.php';

        if (! file_exists($routeFile)) {
            return;
        }

        // Routes already have prefix and middleware in route files, just require them
        require $routeFile;
    }
}
