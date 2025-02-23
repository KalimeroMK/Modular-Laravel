<?php

namespace App\Modules\Auth\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Modules\Core\Models\Core;
use App\Modules\Auth\database\factories\AuthFactory;

class Auth extends Core
{

/** @use HasFactory<AuthFactory> */
    use HasFactory;

    protected $table = 'auths';

    protected $fillable = [
        // Define fillable fields here
    ];

    public static function factory(): AuthFactory
    {
        return AuthFactory::new();
    }
}