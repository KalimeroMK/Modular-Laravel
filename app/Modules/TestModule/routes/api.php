<?php

use Illuminate\Support\Facades\Route;
use App\Modules\TestModule\Http\Controllers\TestModuleController;

Route::prefix('api/v1')->group(function () {
    Route::apiResource('test_modules', TestModuleController::class);
});
