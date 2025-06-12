<?php

namespace App\Modules\User\Http\Actions;

use App\Modules\User\Models\User;

class DeleteUserAction
{
    public function execute(User $user): void
    {
        $user->delete();
    }
}
