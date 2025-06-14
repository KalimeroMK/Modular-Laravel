<?php

declare(strict_types=1);

namespace App\Modules\Role\Models;

use App\Modules\Role\database\factories\RoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    /** @use HasFactory<RoleFactory> */
    use HasFactory;

    protected $attributes = [
        'guard_name' => 'web',
    ];

    protected $table = 'roles';

    public static function factory(): RoleFactory
    {
        return RoleFactory::new();
    }
}
