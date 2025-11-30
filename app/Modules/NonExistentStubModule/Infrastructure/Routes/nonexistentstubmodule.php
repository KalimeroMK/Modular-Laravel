<?php

declare(strict_types=1);

use App\Modules\NonExistentStubModule\Infrastructure\Http\Controllers\NonExistentStubModuleController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/test_modules')->middleware(['auth:sanctum'])->group(function (): void {
    // NonExistentStubModule resource routes with Laravel default rate limiting
    Route::get('/', [NonExistentStubModuleController::class, 'index'])
        ->middleware('throttle:120,1'); // 120 requests per minute
    
    Route::post('/', [NonExistentStubModuleController::class, 'store'])
        ->middleware('throttle:20,60'); // 20 requests per hour
    
    Route::get('/{ nonexistentstubmodule }', [NonExistentStubModuleController::class, 'show'])
        ->middleware('throttle:120,1'); // 120 requests per minute
    
    Route::put('/{ nonexistentstubmodule }', [NonExistentStubModuleController::class, 'update'])
        ->middleware('throttle:20,60'); // 20 requests per hour
    
    Route::delete('/{ nonexistentstubmodule }', [NonExistentStubModuleController::class, 'destroy'])
        ->middleware('throttle:5,60'); // 5 requests per hour
});
