<?php

use App\Modules\Role\Http\Controllers\Api\RoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->group(function (): void {
    Route::apiResource('roles', RoleController::class);
});
