<?php

declare(strict_types=1);

use App\Modules\Role\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->group(function (): void {
    Route::apiResource('roles', RoleController::class);
});
