<?php

declare(strict_types=1);

use App\Modules\Role\Infrastructure\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::crud('roles', RoleController::class);
