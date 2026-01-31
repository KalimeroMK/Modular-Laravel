<?php

declare(strict_types=1);

namespace App\Modules\Permission\Infrastructure\Policies;

use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\User\Infrastructure\Models\User;

class PermissionPolicy
{
    


    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    


    public function view(User $user, Permission $permission): bool
    {
        return $user->hasRole('admin');
    }

    


    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    


    public function update(User $user, Permission $permission): bool
    {
        return $user->hasRole('admin');
    }

    


    public function delete(User $user, Permission $permission): bool
    {
        return $user->hasRole('admin');
    }

    


    public function restore(User $user, Permission $permission): bool
    {
        return $user->hasRole('admin');
    }

    


    public function forceDelete(User $user, Permission $permission): bool
    {
        return $user->hasRole('admin');
    }
}
