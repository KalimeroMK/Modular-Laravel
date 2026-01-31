<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));


Route::get('/password/reset/{token}', fn () => response()->json(['message' => 'Password reset page']))->name('password.reset');
