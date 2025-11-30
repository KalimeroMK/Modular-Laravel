<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Actions;

use App\Modules\User\Application\DTO\UserResponseDTO;
use App\Modules\User\Infrastructure\Models\User;

class GetUserByIdAction
{
    public function __construct() {}

    public function execute(User $user): ?UserResponseDTO
    {
        // Model is already resolved via route model binding, no need to query again
        return UserResponseDTO::fromUser($user);
    }
}
