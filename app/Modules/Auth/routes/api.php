<?php

declare(strict_types=1);

use App\Modules\Auth\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->group(function (): void {
    // Laravel default rate limiting - direct limits, no custom configuration
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('throttle:5,15'); // 5 attempts per 15 minutes

    Route::post('register', [AuthController::class, 'register'])
        ->middleware('throttle:3,60'); // 3 attempts per hour

    Route::post('logout', [AuthController::class, 'logout'])
        ->middleware(['auth:sanctum', 'throttle:60,1']); // 60 per minute

    Route::get('me', [AuthController::class, 'me'])
        ->middleware(['auth:sanctum', 'throttle:120,1']); // 120 per minute

    Route::post('forgot-password', [AuthController::class, 'sendResetLink'])
        ->name('password.reset')
        ->middleware('throttle:3,60'); // 3 attempts per hour

    Route::post('reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:3,60'); // 3 attempts per hour
});
