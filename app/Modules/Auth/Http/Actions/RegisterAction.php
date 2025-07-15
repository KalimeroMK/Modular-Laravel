<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Actions;

use App\Modules\Auth\Http\DTOs\RegisterDTO;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterAction
{
    public function __construct(protected UserRepository $repository) {}

    /**
     * @return array{user: \App\Modules\User\Models\User, token: string}
     */
    public function execute(RegisterDTO $dto): array
    {
        $user = $this->repository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);

        if (!$user instanceof User) {
            throw ValidationException::withMessages(['email' => __('auth.registration_failed')]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
