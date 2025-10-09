<?php

declare(strict_types=1);

use App\Modules\User\Infrastructure\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/users')->middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/', [UserController::class, 'index'])
        ->middleware('throttle:60,1'); // 60 per minute

    Route::get('/{user}', [UserController::class, 'show'])
        ->middleware('throttle:120,1'); // 120 per minute

    Route::post('/', [UserController::class, 'store'])
        ->middleware('throttle:10,60'); // 10 per hour

    Route::put('/{user}', [UserController::class, 'update'])
        ->middleware('throttle:30,1'); // 30 per minute

    Route::delete('/{user}', [UserController::class, 'destroy'])
        ->middleware('throttle:10,60'); // 10 per hour
});
