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

    public function getGuardName(): string
    {
        $properties = get_object_vars($this);
        if (isset($properties['guard_name']) && is_string($properties['guard_name'])) {
            return $properties['guard_name'];
        }

        return config('auth.defaults.guard', 'web');
    }

    public function assignRole(Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->where('guard_name', $this->getGuardName())->firstOrFail();
        }

        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    public function removeRole(Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->where('guard_name', $this->getGuardName())->firstOrFail();
        }

        $this->roles()->detach($role->id);
    }

    public function givePermissionTo(Permission|string|array $permission): void
    {
        $permissions = is_array($permission) ? $permission : [$permission];
        $ids = [];

        foreach ($permissions as $perm) {
            if (is_string($perm)) {
                $perm = Permission::where('name', $perm)->where('guard_name', $this->getGuardName())->firstOrFail();
            }
            $ids[] = $perm->id;
        }

        $this->permissions()->syncWithoutDetaching($ids);
    }

    public function revokePermissionTo(Permission|string|array $permission): void
    {
        $permissions = is_array($permission) ? $permission : [$permission];
        $ids = [];

        foreach ($permissions as $perm) {
            if (is_string($perm)) {
                $perm = Permission::where('name', $perm)->where('guard_name', $this->getGuardName())->firstOrFail();
            }
            $ids[] = $perm->id;
        }

        $this->permissions()->detach($ids);
    }

    public function hasRole(Role|string $role): bool
    {
        if (is_string($role)) {
            return $this->roles()->where('name', $role)->where('guard_name', $this->getGuardName())->exists();
        }

        return $this->roles()->where('roles.id', $role->id)->exists();
    }

    public function hasPermissionTo(Permission|string $permission): bool
    {
        if (is_string($permission)) {
            if ($this->permissions()->where('name', $permission)->where('guard_name', $this->getGuardName())->exists()) {
                return true;
            }

            return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
                $query->where('name', $permission)->where('guard_name', $this->getGuardName());
            })->exists();
        }

        if ($this->permissions()->where('permissions.id', $permission->id)->exists()) {
            return true;
        }

        return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('permissions.id', $permission->id);
        })->exists();
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

    public function hasAllRoles(array $roles): bool
    {
        foreach ($roles as $role) {
            if (! $this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    public function syncRoles(array $roles): void
    {
        $ids = collect($roles)->map(function (Role|string $role) {
            if (is_string($role)) {
                $role = Role::where('name', $role)->where('guard_name', $this->getGuardName())->firstOrFail();
            }

            return $role->id;
        })->all();

        $this->roles()->sync($ids);
    }

    public function syncPermissions(array $permissions): void
    {
        $ids = collect($permissions)->map(function (Permission|string $permission) {
            if (is_string($permission)) {
                $permission = Permission::where('name', $permission)->where('guard_name', $this->getGuardName())->firstOrFail();
            }

            return $permission->id;
        })->all();

        $this->permissions()->sync($ids);
    }

    public function getRoleNames(): array
    {
        return $this->roles()->pluck('name')->toArray();
    }

    public function getPermissionNames(): array
    {
        return $this->permissions()->pluck('name')->toArray();
    }
}
