<?php

namespace App\Modules\Permission\Models;

use App\Modules\Permission\database\factories\PermissionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    /** @use HasFactory<PermissionFactory> */
    use HasFactory;

    protected $table = 'permissions';

    public static function factory(): PermissionFactory
    {
        return PermissionFactory::new();
    }
}
