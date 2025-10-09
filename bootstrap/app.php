<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Laravel built-in throttle middleware is already available
        // No custom middleware needed - use throttle:name or throttle:max,decay
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withProviders([
        App\Modules\Auth\Infrastructure\Providers\AuthModuleServiceProvider::class,
        App\Modules\User\Infrastructure\Providers\UserModuleServiceProvider::class,
        App\Modules\Role\Infrastructure\Providers\RoleModuleServiceProvider::class,
        App\Modules\Permission\Infrastructure\Providers\PermissionModuleServiceProvider::class,
    ])->create();
