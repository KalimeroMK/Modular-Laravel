<?php

declare(strict_types=1);

use App\Modules\User\Infrastructure\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::crud('users', UserController::class);
