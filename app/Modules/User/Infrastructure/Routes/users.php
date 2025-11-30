<?php

declare(strict_types=1);

use App\Modules\User\Infrastructure\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/users')->middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/', [UserController::class, 'index'])
        ->middleware('throttle:60,1')
        ->name('users.index');

    Route::post('/', [UserController::class, 'store'])
        ->middleware('throttle:10,60')
        ->name('users.store');

    Route::get('/{id}', [UserController::class, 'show'])
        ->middleware('throttle:120,1')
        ->name('users.show');

    Route::put('/{id}', [UserController::class, 'update'])
        ->middleware('throttle:30,1')
        ->name('users.update');

    Route::patch('/{id}', [UserController::class, 'update'])
        ->middleware('throttle:30,1')
        ->name('users.patch');

    Route::delete('/{id}', [UserController::class, 'destroy'])
        ->middleware('throttle:10,60')
        ->name('users.destroy');
});
