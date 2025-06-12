<?php

namespace App\Modules\Auth\Http\Actions;

use Illuminate\Support\Facades\Hash;
use App\Modules\Auth\Http\DTOs\RegisterDTO;
use App\Modules\User\Repositories\UserRepository;

class RegisterAction
{
    public function __construct(protected UserRepository $repository) {}

    public function execute(RegisterDTO $dto): mixed
    {
        return $this->repository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);
    }
}
