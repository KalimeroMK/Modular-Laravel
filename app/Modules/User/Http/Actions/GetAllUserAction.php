<?php

namespace App\Modules\User\Http\Actions;

use App\Modules\User\Models\User;
use Illuminate\Support\Collection;

class GetAllUserAction
{
    public function execute(): Collection
    {
        return User::all();
    }
}
