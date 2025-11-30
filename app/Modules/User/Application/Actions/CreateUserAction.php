<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Actions;

use App\Modules\Core\Exceptions\CreateException;
use App\Modules\User\Application\DTO\CreateUserDTO;
use App\Modules\User\Infrastructure\Models\User;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class CreateUserAction
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
    ) {}

    public function execute(CreateUserDTO $dto): User
    {
        $userData = [
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'email_verified_at' => $dto->emailVerifiedAt,
        ];

        /** @var User|null $user */
        $user = $this->userRepository->create($userData);

        if ($user === null) {
            throw new CreateException('Failed to create user');
        }

        return $user;
    }
}
