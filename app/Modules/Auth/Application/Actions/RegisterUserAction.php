<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Actions;

use App\Modules\Auth\Application\DTO\RegisterUserDTO;
use App\Modules\Auth\Application\DTO\UserResponseDTO;
use App\Modules\Auth\Application\Services\IssueTokenServiceInterface;
use App\Modules\Auth\Infrastructure\Repositories\AuthRepositoryInterface;
use App\Modules\User\Infrastructure\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class RegisterUserAction
{
    public function __construct(
        protected AuthRepositoryInterface $authRepository,
        protected IssueTokenServiceInterface $tokenService,
    ) {}

    public function execute(RegisterUserDTO $dto): array
    {
        $userData = [
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ];

        $user = $this->authRepository->create($userData);

        if (! $user instanceof User) {
            throw new Exception('Failed to create user');
        }

        $tokenDTO = $this->tokenService->issueToken($user);

        return [
            'user' => UserResponseDTO::fromUser($user),
            'token' => $tokenDTO->token,
        ];
    }
}
