<?php

declare(strict_types=1);

use App\Modules\Permission\Infrastructure\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/permissions')->middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/', [PermissionController::class, 'index'])
        ->middleware('throttle:60,1'); // 60 per minute

    Route::get('/{permission}', [PermissionController::class, 'show'])
        ->middleware('throttle:120,1'); // 120 per minute

    Route::post('/', [PermissionController::class, 'store'])
        ->middleware('throttle:10,60'); // 10 per hour

    Route::put('/{permission}', [PermissionController::class, 'update'])
        ->middleware('throttle:30,1'); // 30 per minute

    Route::delete('/{permission}', [PermissionController::class, 'destroy'])
        ->middleware('throttle:10,60'); // 10 per hour
});
