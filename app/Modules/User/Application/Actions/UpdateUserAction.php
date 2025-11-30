<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Actions;

use App\Modules\Core\Exceptions\UpdateException;
use App\Modules\User\Application\DTO\UpdateUserDTO;
use App\Modules\User\Infrastructure\Models\User;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UpdateUserAction
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
    ) {}

    public function execute(int $id, UpdateUserDTO $dto): User
    {
        // Validate that the user exists
        $this->userRepository->findOrFail($id);

        $updateData = $dto->toArray();

        // Hash password if provided
        if (isset($updateData['password'])) {
            $updateData['password'] = Hash::make($updateData['password']);
        }

        // Only include non-null values in the update
        $updateData = array_filter($updateData, fn ($value) => $value !== null);

        /** @var User|null $updatedUser */
        $updatedUser = $this->userRepository->update($id, $updateData);

        if ($updatedUser === null) {
            throw new UpdateException('Failed to update user');
        }

        return $updatedUser;
    }
}
