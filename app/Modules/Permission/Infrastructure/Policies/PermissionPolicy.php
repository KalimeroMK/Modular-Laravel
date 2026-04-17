<?php

declare(strict_types=1);

namespace App\Modules\Permission\Infrastructure\Policies;

use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\User\Infrastructure\Models\User;

class PermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage-permissions');
    }

    public function view(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo('manage-permissions');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage-permissions');
    }

    public function update(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo('manage-permissions');
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo('manage-permissions');
    }

    public function restore(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo('manage-permissions');
    }

    public function forceDelete(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo('manage-permissions');
    }
}
