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
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/api/health',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        
        $exceptions->render(fn (BaseException $e) => $e->render());

        
        $exceptions->map(fn (ModelNotFoundException $e) => new NotFoundHttpException('Resource not found', $e));

        
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'error_code' => 'RESOURCE_NOT_FOUND',
                    'message' => 'Resource not found',
                    'errors' => [],
                ], 404);
            }

            return null; 
        });

        
        $exceptions->render(function (LaravelValidationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'error_code' => 'VALIDATION_ERROR',
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }

            return null; 
        });
    })
    ->withProviders([
        App\Modules\Auth\Infrastructure\Providers\AuthModuleServiceProvider::class,
        App\Modules\User\Infrastructure\Providers\UserModuleServiceProvider::class,
        App\Modules\Role\Infrastructure\Providers\RoleModuleServiceProvider::class,
        App\Modules\Permission\Infrastructure\Providers\PermissionModuleServiceProvider::class])
    ->create();
