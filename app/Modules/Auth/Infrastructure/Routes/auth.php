<?php

declare(strict_types=1);

use App\Modules\Auth\Infrastructure\Http\Controllers\AuthController;
use App\Modules\Auth\Infrastructure\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/auth')->group(function (): void {
    
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('throttle:5,15'); 

    Route::post('register', [AuthController::class, 'register'])
        ->middleware('throttle:3,60'); 

    Route::post('forgot-password', [AuthController::class, 'sendResetLink'])
        ->middleware('throttle:3,60'); 

    Route::post('reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:3,60'); 

    Route::get('reset-password/{token}', static function (string $token) {
        return response()->json([
            'token' => $token,
        ]);
    })->name('password.reset');

    
    Route::middleware(['auth:sanctum'])->group(function (): void {
        Route::get('me', [AuthController::class, 'me'])
            ->middleware('throttle:120,1'); 

        Route::post('logout', [AuthController::class, 'logout'])
            ->middleware('throttle:60,1'); 

        
        Route::prefix('2fa')->group(function (): void {
            Route::get('status', [TwoFactorController::class, 'status'])
                ->middleware('throttle:120,1'); 

            Route::post('setup', [TwoFactorController::class, 'setup'])
                ->middleware('throttle:3,60'); 

            Route::post('verify', [TwoFactorController::class, 'verify'])
                ->middleware('throttle:10,1'); 

            Route::delete('disable', [TwoFactorController::class, 'disable'])
                ->middleware('throttle:3,60'); 

            Route::post('recovery-codes', [TwoFactorController::class, 'generateRecoveryCodes'])
                ->middleware('throttle:3,60'); 
        });
    });
});
