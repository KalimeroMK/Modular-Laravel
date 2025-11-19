<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Actions;

use App\Modules\Core\Exceptions\UpdateException;
use App\Modules\User\Application\DTO\UpdateUserDTO;
use App\Modules\User\Application\DTO\UserResponseDTO;
use App\Modules\User\Infrastructure\Models\User;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;
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

        // Get the user ID - route model binding ensures the model exists
        // Use getKey() as primary method, fallback to id property
        $userId = (int) ($user->getKey() ?: ($user->id ?? 0));

        if ($userId === 0) {
            // If ID is still 0, try to get it from the route parameter
            $routeUser = request()->route('user');
            if ($routeUser instanceof User) {
                $userId = (int) ($routeUser->getKey() ?: ($routeUser->id ?? 0));
            }

            if ($userId === 0) {
                // This should not happen with route model binding, but handle it gracefully
                throw new UpdateException('Invalid user ID: User model must have an ID');
            }
        }

        /** @var User $updatedUser */
        $updatedUser = $this->userRepository->update($userId, $updateData);

        if ($updatedUser === null) {
            throw new UpdateException('Failed to update user');
        }

        return UserResponseDTO::fromUser($updatedUser);
    }
}
