<?php

declare(strict_types=1);

use App\Modules\User\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->middleware(['auth:sanctum'])->group(function (): void {
    // User resource routes with Laravel default rate limiting
    Route::get('users', [UserController::class, 'index'])
        ->middleware('throttle:120,1'); // 120 per minute

    Route::post('users', [UserController::class, 'store'])
        ->middleware('throttle:20,60'); // 20 per hour

    Route::get('users/{user}', [UserController::class, 'show'])
        ->middleware('throttle:120,1'); // 120 per minute

    Route::put('users/{user}', [UserController::class, 'update'])
        ->middleware('throttle:20,60'); // 20 per hour

    Route::delete('users/{user}', [UserController::class, 'destroy'])
        ->middleware('throttle:5,60'); // 5 per hour
});
