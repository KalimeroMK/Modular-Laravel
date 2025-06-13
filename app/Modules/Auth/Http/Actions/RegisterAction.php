<?php

namespace App\Modules\Auth\Http\Actions;

use App\Modules\Auth\Http\DTOs\RegisterDTO;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class RegisterAction
{
    public function __construct(protected UserRepository $repository) {}

    public function execute(RegisterDTO $dto): array
    {
        $user = $this->repository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
