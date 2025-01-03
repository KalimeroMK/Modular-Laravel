<?php

namespace App\Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\User\database\factories\UserFactory;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        // Define fillable fields here
    ];

    public static function factory(): UserFactory
    {
        return UserFactory::new();
    }
}
