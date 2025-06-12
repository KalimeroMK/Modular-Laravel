<?php

namespace App\Modules\User\Http\Actions;

use App\Modules\User\Models\User;

class GetUserByIdAction
{
    public function execute(User $user): User
    {
        return $user;
    }
}
