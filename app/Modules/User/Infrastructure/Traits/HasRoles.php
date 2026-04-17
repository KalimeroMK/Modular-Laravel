<?php

declare(strict_types=1);

namespace App\Modules\User\Infrastructure\Traits;

use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Role\Infrastructure\Models\Role;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasRoles
{
    public function roles(): MorphToMany
    {
        return $this->morphToMany(
            Role::class,
            'model',
            'model_has_roles',
            'model_id',
            'role_id'
        )->wherePivot('model_type', static::class);
    }

    public function permissions(): MorphToMany
    {
        return $this->morphToMany(
            Permission::class,
            'model',
            'model_has_permissions',
            'model_id',
            'permission_id'
        )->wherePivot('model_type', static::class);
    }

    public function assignRole(Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->where('guard_name', 'api')->firstOrFail();
        }

        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    public function removeRole(Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->where('guard_name', 'api')->firstOrFail();
        }

        $this->roles()->detach($role->id);
    }

    public function givePermissionTo(Permission|string $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->where('guard_name', 'api')->firstOrFail();
        }

        $this->permissions()->syncWithoutDetaching([$permission->id]);
    }

    public function revokePermissionTo(Permission|string $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->where('guard_name', 'api')->firstOrFail();
        }

        $this->permissions()->detach($permission->id);
    }

    public function hasRole(Role|string $role): bool
    {
        if (is_string($role)) {
            return $this->roles()->where('name', $role)->where('guard_name', 'api')->exists();
        }

        return $this->roles()->where('roles.id', $role->id)->exists();
    }

    public function hasPermissionTo(Permission|string $permission): bool
    {

        if (is_string($permission)) {
            $hasDirectPermission = $this->permissions()->where('name', $permission)->where('guard_name', 'api')->exists();
        } else {
            $hasDirectPermission = $this->permissions()->where('permissions.id', $permission->id)->exists();
        }

        if ($hasDirectPermission) {
            return true;
        }

        $rolePermissions = $this->roles()->with('permissions')->get()->pluck('permissions')->flatten();

        if (is_string($permission)) {
            return $rolePermissions->where('name', $permission)->where('guard_name', 'api')->isNotEmpty();
        }

        return $rolePermissions->where('id', $permission->id)->isNotEmpty();
    }

    public function hasAnyRole(array|string $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    public function syncRoles(array $roles): void
    {
        $ids = collect($roles)->map(function (Role|string $role) {
            if (is_string($role)) {
                $role = Role::where('name', $role)->where('guard_name', 'api')->firstOrFail();
            }

            return $role->id;
        })->all();

        $this->roles()->sync($ids);
    }

    public function syncPermissions(array $permissions): void
    {
        $ids = collect($permissions)->map(function (Permission|string $permission) {
            if (is_string($permission)) {
                $permission = Permission::where('name', $permission)->where('guard_name', 'api')->firstOrFail();
            }

            return $permission->id;
        })->all();

        $this->permissions()->sync($ids);
    }

    public function hasAllRoles(array $roles): bool
    {
        foreach ($roles as $role) {
            if (! $this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }
}
