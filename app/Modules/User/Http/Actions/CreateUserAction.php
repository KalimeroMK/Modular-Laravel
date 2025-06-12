<?php

namespace App\Modules\User\Http\Actions;

use App\Modules\User\Http\DTOs\CreateUserDTO;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\User\Models\User;

class CreateUserAction
{
    public function __construct(protected UserRepository $repository) {}

    public function execute(CreateUserDTO $dto): User
    {
        return $this->repository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => $dto->password,
        ]);
    }
}
