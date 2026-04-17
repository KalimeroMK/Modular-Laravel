<?php

declare(strict_types=1);

namespace App\Modules\Auth\Infrastructure\Providers;

use App\Modules\Auth\Application\Services\IssueTokenService;
use App\Modules\Auth\Application\Services\IssueTokenServiceInterface;
use App\Modules\Auth\Application\Services\TwoFactor\Service;
use App\Modules\Auth\Application\Services\TwoFactor\ServiceInterface;
use App\Modules\Auth\Infrastructure\Repositories\AuthRepository;
use App\Modules\Auth\Infrastructure\Repositories\AuthRepositoryInterface;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Support\ServiceProvider;
use PragmaRX\Google2FA\Google2FA;

class AuthModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {

        $this->app->bind(AuthRepositoryInterface::class, function ($app) {
            return new AuthRepository($app->make(User::class));
        });
        $this->app->bind(IssueTokenServiceInterface::class, IssueTokenService::class);

        $this->app->bind(ServiceInterface::class, Service::class);
        $this->app->singleton(Google2FA::class, fn () => new Google2FA());
    }

    public function boot(): void
    {

        if (! $this->isModuleEnabled()) {
            return;
        }

        $this->loadRoutes();
    }

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

        require $routeFile;
    }
}
