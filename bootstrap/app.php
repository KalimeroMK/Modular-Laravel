<?php

declare(strict_types=1);

use App\Modules\Core\Exceptions\BaseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        // Handle custom BaseException classes
        $exceptions->render(fn (BaseException $e) => $e->render());

        // Handle ModelNotFoundException - map it before it gets converted to NotFoundHttpException
        $exceptions->map(fn (ModelNotFoundException $e) => new NotFoundHttpException('Resource not found', $e));

        // Handle NotFoundHttpException for API requests (which includes ModelNotFoundException)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'error_code' => 'RESOURCE_NOT_FOUND',
                    'message' => 'Resource not found',
                    'errors' => [],
                ], 404);
            }

            return null; // Let Laravel handle it for non-API requests
        });

        // Handle Laravel ValidationException
        $exceptions->render(fn (LaravelValidationException $e) => response()->json([
            'status' => 'error',
            'error_code' => 'VALIDATION_ERROR',
            'message' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422));
    })
    ->withProviders([
        App\Modules\Auth\Infrastructure\Providers\AuthModuleServiceProvider::class,
        App\Modules\User\Infrastructure\Providers\UserModuleServiceProvider::class,
        App\Modules\Role\Infrastructure\Providers\RoleModuleServiceProvider::class,
        App\Modules\Permission\Infrastructure\Providers\PermissionModuleServiceProvider::class,
    ])
    ->create();
