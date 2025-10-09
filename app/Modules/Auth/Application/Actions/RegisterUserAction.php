<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Actions;

use App\Modules\Auth\Application\DTO\RegisterUserDTO;
use App\Modules\Auth\Application\DTO\UserResponseDTO;
use App\Modules\Auth\Application\Services\IssueTokenServiceInterface;
use App\Modules\Auth\Infrastructure\Repositories\AuthRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Hash;

class RegisterUserAction
{
    public function __construct(
        protected AuthRepositoryInterface $authRepository,
        protected IssueTokenServiceInterface $tokenService,
    ) {}

    /**
     * @return array{user: UserResponseDTO, token: string}
     */
    public function execute(RegisterUserDTO $dto): array
    {
        $userData = [
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ];

        /** @var \App\Modules\User\Infrastructure\Models\User $user */
        $user = $this->authRepository->create($userData);

        if ($user === null) {
            throw new Exception('Failed to create user');
        }

        $tokenDTO = $this->tokenService->issueToken($user);

        return [
            'user' => UserResponseDTO::fromUser($user),
            'token' => $tokenDTO->token,
        ];
    }
}
