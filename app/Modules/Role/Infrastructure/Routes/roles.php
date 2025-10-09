<?php

declare(strict_types=1);

use App\Modules\Role\Infrastructure\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/roles')->middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/', [RoleController::class, 'index'])
        ->middleware('throttle:60,1'); // 60 per minute

    Route::get('/{role}', [RoleController::class, 'show'])
        ->middleware('throttle:120,1'); // 120 per minute

    Route::post('/', [RoleController::class, 'store'])
        ->middleware('throttle:10,60'); // 10 per hour

    Route::put('/{role}', [RoleController::class, 'update'])
        ->middleware('throttle:30,1'); // 30 per minute

    Route::delete('/{role}', [RoleController::class, 'destroy'])
        ->middleware('throttle:10,60'); // 10 per hour
});
