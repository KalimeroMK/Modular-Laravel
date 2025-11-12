<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Actions;

use App\Modules\User\Application\DTO\UpdateUserDTO;
use App\Modules\User\Application\DTO\UserResponseDTO;
use App\Modules\User\Infrastructure\Models\User;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Hash;

class UpdateUserAction
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
    ) {}

    public function execute(User $user, UpdateUserDTO $dto): UserResponseDTO
    {
        $updateData = $dto->toArray();

        // Hash password if provided
        if (isset($updateData['password'])) {
            $updateData['password'] = Hash::make($updateData['password']);
        }

        /** @var User $updatedUser */
        $updatedUser = $this->userRepository->update($user->id, $updateData);

        if ($updatedUser === null) {
            throw new Exception('Failed to update user');
        }

        return UserResponseDTO::fromUser($updatedUser);
    }
}
