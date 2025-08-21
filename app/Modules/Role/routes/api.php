<?php

declare(strict_types=1);

use App\Modules\Role\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->middleware(['auth:sanctum'])->group(function (): void {
    // Role management with Laravel default rate limiting
    Route::get('roles', [RoleController::class, 'index'])
        ->middleware('throttle:120,1'); // 120 per minute

    Route::post('roles', [RoleController::class, 'store'])
        ->middleware('throttle:20,60'); // 20 per hour

    Route::get('roles/{role}', [RoleController::class, 'show'])
        ->middleware('throttle:120,1'); // 120 per minute

    Route::put('roles/{role}', [RoleController::class, 'update'])
        ->middleware('throttle:20,60'); // 20 per hour

    Route::delete('roles/{role}', [RoleController::class, 'destroy'])
        ->middleware('throttle:5,60'); // 5 per hour
});
