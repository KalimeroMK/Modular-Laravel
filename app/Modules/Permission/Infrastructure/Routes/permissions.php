<?php

declare(strict_types=1);

use App\Modules\Permission\Infrastructure\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/permissions')->middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/', [PermissionController::class, 'index'])
        ->middleware('throttle:60,1')
        ->name('permissions.index');

    Route::post('/', [PermissionController::class, 'store'])
        ->middleware('throttle:10,60')
        ->name('permissions.store');

    Route::get('/{permission}', [PermissionController::class, 'show'])
        ->middleware('throttle:120,1')
        ->name('permissions.show');

    Route::put('/{permission}', [PermissionController::class, 'update'])
        ->middleware('throttle:30,1')
        ->name('permissions.update');

    Route::patch('/{permission}', [PermissionController::class, 'update'])
        ->middleware('throttle:30,1')
        ->name('permissions.patch');

    Route::delete('/{permission}', [PermissionController::class, 'destroy'])
        ->middleware('throttle:10,60')
        ->name('permissions.destroy');
});
