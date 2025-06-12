<?php

namespace App\Modules\User\Http\Actions;

use App\Modules\User\Http\DTOs\UpdateUserDTO;
use App\Modules\User\Models\User;

class UpdateUserAction
{
    public function execute(User $user, UpdateUserDTO $dto): User
    {
        $user->update([
            'name' => $dto->name,
            'email' => $dto->email,
        ]);

        return $user->fresh();
    }
}
