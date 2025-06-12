<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Http\Controllers\Api\UserController;

Route::resource('users', UserController::class);
