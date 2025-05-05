<?php

use App\Modules\User\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->group(function (): void {
    Route::apiResource('users', UserController::class);
});
