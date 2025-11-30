<?php

declare(strict_types=1);

namespace App\Modules\Permission\Infrastructure\Policies;

use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\User\Infrastructure\Models\User;

class PermissionPolicy
{
    /**
     * Determine if the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, Permission $permission): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can update the model.
     */
    public function update(User $user, Permission $permission): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can delete the model.
     */
    public function delete(User $user, Permission $permission): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can restore the model.
     */
    public function restore(User $user, Permission $permission): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can permanently delete the model.
     */
    public function forceDelete(User $user, Permission $permission): bool
    {
        return $user->hasRole('admin');
    }
}
