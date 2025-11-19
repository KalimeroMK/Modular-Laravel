<?php

declare(strict_types=1);

namespace App\Modules\User\Infrastructure\Traits;

use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Role\Infrastructure\Models\Role;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasRoles
{
    /**
     * Get all roles for the user.
     *
     * @return MorphToMany<Role, \Illuminate\Database\Eloquent\Model>
     */
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

    /**
     * Get all permissions for the user (direct and via roles).
     *
     * @return MorphToMany<Permission, \Illuminate\Database\Eloquent\Model>
     */
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

    /**
     * Assign a role to the user.
     */
    public function assignRole(Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->where('guard_name', 'api')->firstOrFail();
        }

        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole(Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->where('guard_name', 'api')->firstOrFail();
        }

        $this->roles()->detach($role->id);
    }

    /**
     * Give permission directly to the user.
     */
    public function givePermissionTo(Permission|string $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->where('guard_name', 'api')->firstOrFail();
        }

        $this->permissions()->syncWithoutDetaching([$permission->id]);
    }

    /**
     * Revoke permission from the user.
     */
    public function revokePermissionTo(Permission|string $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->where('guard_name', 'api')->firstOrFail();
        }

        $this->permissions()->detach($permission->id);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(Role|string $role): bool
    {
        if (is_string($role)) {
            return $this->roles()->where('name', $role)->where('guard_name', 'api')->exists();
        }

        return $this->roles()->where('roles.id', $role->id)->exists();
    }

    /**
     * Check if user has a specific permission (direct or via role).
     */
    public function hasPermissionTo(Permission|string $permission): bool
    {
        // Check direct permissions
        if (is_string($permission)) {
            $hasDirectPermission = $this->permissions()->where('name', $permission)->where('guard_name', 'api')->exists();
        } else {
            $hasDirectPermission = $this->permissions()->where('permissions.id', $permission->id)->exists();
        }

        if ($hasDirectPermission) {
            return true;
        }

        // Check permissions via roles
        $rolePermissions = $this->roles()->with('permissions')->get()->pluck('permissions')->flatten();

        if (is_string($permission)) {
            return $rolePermissions->where('name', $permission)->where('guard_name', 'api')->isNotEmpty();
        }

        return $rolePermissions->where('id', $permission->id)->isNotEmpty();
    }

    /**
     * Check if user has any of the given roles.
     *
     * @param  array<int, Role|string>|string  $roles
     */
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

    /**
     * Check if user has all of the given roles.
     *
     * @param  array<int, Role|string>  $roles
     */
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
