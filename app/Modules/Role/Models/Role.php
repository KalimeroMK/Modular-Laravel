<?php

namespace App\Modules\Role\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Modules\Role\database\factories\RoleFactory;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{

/** @use HasFactory<RoleFactory> */
    use HasFactory;

    protected $table = 'roles';

    public static function factory(): RoleFactory
    {
        return RoleFactory::new();
    }


}