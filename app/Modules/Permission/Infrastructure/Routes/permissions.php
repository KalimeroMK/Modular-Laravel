<?php

declare(strict_types=1);

use App\Modules\Permission\Infrastructure\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::crud('permissions', PermissionController::class);
