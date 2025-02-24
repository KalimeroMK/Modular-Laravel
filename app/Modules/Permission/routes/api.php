<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Permission\Http\Controllers\Api\PermissionController;

Route::prefix('api/v1')->group(function () {
    Route::apiResource('permissions', PermissionController::class);
});
