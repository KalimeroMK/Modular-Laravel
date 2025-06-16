<?php

declare(strict_types=1);

namespace App\Modules\Permission\Models;

use App\Modules\Permission\Database\Factories\PermissionFactory;
use App\Modules\Role\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    protected $table = 'permissions';

    protected $attributes = [
        'guard_name' => 'web',
    ];

    protected $fillable = [
        'name',
        'guard_name',
    ];

    public static function factory(): PermissionFactory
    {
        return PermissionFactory::new();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions');
    }
}
