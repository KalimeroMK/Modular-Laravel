<?php

declare(strict_types=1);

namespace App\Modules\User\Infrastructure\Models;

use App\Modules\User\Database\Factories\UserFactory;
use App\Modules\User\Infrastructure\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Override;

class User extends Authenticatable
{
     
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $table = 'users';

    




    protected array $dates = [
        'email_verified_at',
        'two_factor_confirmed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    


    
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    


    


    protected static function newFactory(): Factory|UserFactory
    {
        return UserFactory::new();
    }
}
