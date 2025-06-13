<?php

namespace App\Modules\Auth\Http\Actions;

use App\Modules\Auth\Http\DTOs\LoginDTO;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginAction
{
    public function __construct(protected UserRepository $repository) {}

    public function execute(LoginDTO $dto): array
    {
        if (! Auth::attempt(['email' => $dto->email, 'password' => $dto->password])) {
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        $user = $this->repository->findBy('email', $dto->email);

        return [
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken,
        ];
    }
}
