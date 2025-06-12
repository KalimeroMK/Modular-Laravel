<?php

namespace App\Modules\User\Http\Actions;

use App\Modules\User\Interfaces\UserInterface;
use App\Modules\User\Models\User;

class DeleteUserAction
{
    public function __construct(protected UserInterface $repository) {}

    public function execute(User $user): void
    {
        $this->repository->delete($user->id);
    }
}