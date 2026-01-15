<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

// Password reset route for Laravel's built-in password reset functionality
Route::get('/password/reset/{token}', fn () => response()->json(['message' => 'Password reset page']))->name('password.reset');
