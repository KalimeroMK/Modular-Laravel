<?php

use App\Modules\Permission\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->group(function (): void {
    Route::apiResource('permissions', PermissionController::class);
});
