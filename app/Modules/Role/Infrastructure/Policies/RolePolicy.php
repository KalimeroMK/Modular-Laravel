<?php

declare(strict_types=1);

namespace App\Modules\Role\Infrastructure\Policies;

use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\User\Infrastructure\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, Role $role): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Role $role): bool
    {
        return $user->hasRole('admin');
    }
}
