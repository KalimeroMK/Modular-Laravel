<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Http\Controllers\Api\UserController;

Route::prefix('api/v1')->group(function () {
    Route::apiResource('users', UserController::class);
});
