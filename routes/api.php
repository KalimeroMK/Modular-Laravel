<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Global API Routes
|--------------------------------------------------------------------------
|
| This file is for global/shared API routes that don't belong to any module.
| Most API routes should be defined in individual module route files:
| - app/Modules/{Module}/Infrastructure/Routes/api.php
|
| Module routes are automatically registered by their service providers.
|
*/

// Example: Global authenticated user endpoint (can be moved to a module if needed)
Route::middleware('auth:sanctum')->get('/user', fn (Request $request) => $request->user());
