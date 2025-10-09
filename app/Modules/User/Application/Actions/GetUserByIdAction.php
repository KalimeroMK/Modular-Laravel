<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Actions;

use App\Modules\User\Application\DTO\UserResponseDTO;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;
use App\Modules\User\Infrastructure\Models\User;

class GetUserByIdAction
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
    ) {}

    public function execute(int $id): ?UserResponseDTO
    {
        $user = $this->userRepository->find($id);
        
        if (!$user) {
            return null;
        }
        
        return UserResponseDTO::fromUser($user);
    }
}
