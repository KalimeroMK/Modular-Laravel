<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Actions;

use App\Modules\Auth\Http\DTOs\LoginDTO;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginAction
{
    public function __construct(protected UserRepository $repository) {}

    /**
     * @return array{user: User, token: string}
     */
    public function execute(LoginDTO $dto): array
    {
        if (! Auth::attempt(['email' => $dto->email, 'password' => $dto->password])) {
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        /** @var User|null $user */
        $user = $this->repository->findBy('email', $dto->email);

        if ($user === null) {
            throw ValidationException::withMessages(['email' => __('auth.user_not_found')]);
        }

        return [
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken,
        ];
    }
}
