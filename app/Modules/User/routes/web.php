<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Http\Controllers\UserController;

Route::resource('users', UserController::class);
