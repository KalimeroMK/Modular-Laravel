<?php

namespace App\Modules\User\Http\Actions;

use App\Modules\User\Http\DTOs\UpdateUserDTO;
use App\Modules\User\Interfaces\UserInterface;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class UpdateUserAction
{
    public function __construct(protected UserInterface $repository) {}

    public function execute(User $user, UpdateUserDTO $dto): Model
    {
        return $this->repository->update($user->id, [
            'name' => $dto->name,
            'email' => $dto->email,
        ]);
    }
}
