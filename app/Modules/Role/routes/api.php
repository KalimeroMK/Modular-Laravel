<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Role\Http\Controllers\Api\RoleController;

Route::prefix('api/v1')->group(function () {
    Route::apiResource('roles', RoleController::class);
});
