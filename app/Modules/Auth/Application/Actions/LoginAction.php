<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Actions;

use App\Modules\Auth\Application\DTO\LoginRequestDTO;
use App\Modules\Auth\Application\DTO\UserResponseDTO;
use App\Modules\Auth\Application\Services\IssueTokenServiceInterface;
use App\Modules\Auth\Infrastructure\Repositories\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginAction
{
    public function __construct(
        protected AuthRepositoryInterface $authRepository,
        protected IssueTokenServiceInterface $tokenService,
    ) {}

    /**
     * @return array{user: UserResponseDTO, token: string}
     */
    public function execute(LoginRequestDTO $dto): array
    {
        if (! Auth::attempt(['email' => $dto->email, 'password' => $dto->password])) {
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        $user = $this->authRepository->findByEmail($dto->email);

        if ($user === null) {
            throw ValidationException::withMessages(['email' => __('auth.user_not_found')]);
        }

        $tokenDTO = $this->tokenService->issueToken($user);

        return [
            'user' => UserResponseDTO::fromUser($user),
            'token' => $tokenDTO->token,
        ];
    }
}
