<?php

declare(strict_types=1);

use App\Modules\Auth\Infrastructure\Http\Controllers\AuthController;
use App\Modules\Auth\Infrastructure\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/auth')->group(function (): void {
    // Public routes
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('throttle:5,15'); // 5 attempts per 15 minutes

    Route::post('register', [AuthController::class, 'register'])
        ->middleware('throttle:3,60'); // 3 attempts per hour

    Route::post('forgot-password', [AuthController::class, 'sendResetLink'])
        ->middleware('throttle:3,60'); // 3 attempts per hour

    Route::post('reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:3,60'); // 3 attempts per hour

    // Protected routes
    Route::middleware(['auth:sanctum'])->group(function (): void {
        Route::get('me', [AuthController::class, 'me'])
            ->middleware('throttle:120,1'); // 120 per minute

        Route::post('logout', [AuthController::class, 'logout'])
            ->middleware('throttle:60,1'); // 60 per minute

        // Two-Factor Authentication routes
        Route::prefix('2fa')->group(function (): void {
            Route::get('status', [TwoFactorController::class, 'status'])
                ->middleware('throttle:120,1'); // 120 per minute

            Route::post('setup', [TwoFactorController::class, 'setup'])
                ->middleware('throttle:3,60'); // 3 attempts per hour

            Route::post('verify', [TwoFactorController::class, 'verify'])
                ->middleware('throttle:10,1'); // 10 attempts per minute

            Route::delete('disable', [TwoFactorController::class, 'disable'])
                ->middleware('throttle:3,60'); // 3 attempts per hour

            Route::post('recovery-codes', [TwoFactorController::class, 'generateRecoveryCodes'])
                ->middleware('throttle:3,60'); // 3 attempts per hour
        });
    });
});
