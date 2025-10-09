<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Actions;

use App\Modules\User\Application\DTO\CreateUserDTO;
use App\Modules\User\Application\DTO\UserResponseDTO;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Hash;

class CreateUserAction
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
    ) {}

    public function execute(CreateUserDTO $dto): UserResponseDTO
    {
        $userData = [
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'email_verified_at' => $dto->emailVerifiedAt,
        ];

        $user = $this->userRepository->create($userData);

        if ($user === null) {
            throw new Exception('Failed to create user');
        }

        return UserResponseDTO::fromUser($user);
    }
}
