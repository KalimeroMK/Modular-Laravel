<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Actions;

use App\Modules\User\Application\DTO\UserResponseDTO;
use App\Modules\User\Infrastructure\Models\User;

class GetUserByIdAction
{
    public function execute(User $user): UserResponseDTO
    {
        return UserResponseDTO::fromUser($user);
    }
}
