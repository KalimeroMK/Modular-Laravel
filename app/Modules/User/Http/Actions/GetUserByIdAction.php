<?php

declare(strict_types=1);

namespace App\Modules\User\Http\Actions;

use App\Modules\User\Interfaces\UserInterface;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class GetUserByIdAction
{
    public function __construct(protected UserInterface $repository) {}

    public function execute(User $user): Model
    {
        return $this->repository->findOrFail($user->id);
    }
}
