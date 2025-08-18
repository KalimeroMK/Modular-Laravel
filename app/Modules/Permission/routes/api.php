<?php

declare(strict_types=1);

use App\Modules\Permission\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->middleware(['auth:sanctum'])->group(function (): void {
    // Permission management with Laravel default rate limiting
    Route::get('permissions', [PermissionController::class, 'index'])
        ->middleware('throttle:120,1'); // 120 per minute
    
    Route::post('permissions', [PermissionController::class, 'store'])
        ->middleware('throttle:20,60'); // 20 per hour
    
    Route::get('permissions/{permission}', [PermissionController::class, 'show'])
        ->middleware('throttle:120,1'); // 120 per minute
    
    Route::put('permissions/{permission}', [PermissionController::class, 'update'])
        ->middleware('throttle:20,60'); // 20 per hour
    
    Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])
        ->middleware('throttle:5,60'); // 5 per hour
});
