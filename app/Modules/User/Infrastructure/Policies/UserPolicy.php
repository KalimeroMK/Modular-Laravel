<?php

declare(strict_types=1);

namespace App\Modules\User\Infrastructure\Policies;

use App\Modules\User\Infrastructure\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return true;
    }

    /**
     * Determine if the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->hasRole('admin');
    }

    /**
     * Determine if the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }
}
