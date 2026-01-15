<?php

declare(strict_types=1);

use App\Modules\NonExistentStubModule\Infrastructure\Http\Controllers\NonExistentStubModuleController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/test_modules')->middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/', [NonExistentStubModuleController::class, 'index'])
        ->middleware('throttle:120,1');
    
    Route::post('/', [NonExistentStubModuleController::class, 'store'])
        ->middleware('throttle:20,60');
    
    Route::get('/{id}', [NonExistentStubModuleController::class, 'show'])
        ->middleware('throttle:120,1');
    
    Route::put('/{id}', [NonExistentStubModuleController::class, 'update'])
        ->middleware('throttle:20,60');
    
    Route::delete('/{id}', [NonExistentStubModuleController::class, 'destroy'])
        ->middleware('throttle:5,60');
});
