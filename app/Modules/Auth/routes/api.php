<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Http\Controllers\Api\AuthController;

Route::prefix('api/v1')->group(function () {
    Route::post('signup', [AuthController::class, 'signup'])->name('auth.signup');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('auth.logout');
    Route::get('user', [AuthController::class, 'getAuthenticatedUser'])->middleware('auth:sanctum')->name('auth.user');
    Route::post('/password/email', [AuthController::class, 'sendPasswordResetLinkEmail'])->middleware(
        'throttle:5,1'
    )->name('password.email');
    Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');
});
