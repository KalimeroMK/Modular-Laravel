<?php

declare(strict_types=1);

namespace App\Modules\User\Http\Actions;

use App\Modules\User\Http\DTOs\CreateUserDTO;
use App\Modules\User\Interfaces\UserInterface;
use App\Modules\User\Models\User;

class CreateUserAction
{
    public function __construct(protected UserInterface $repository) {}

    public function execute(CreateUserDTO $dto): User
    {
        /** @var User $user */
        $user = $this->repository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => bcrypt($dto->password),
        ]);
        return $user;
    }
}
