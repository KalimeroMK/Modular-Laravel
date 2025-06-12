<?php

namespace App\Modules\Auth\Http\Actions;

use Illuminate\Http\Request;

class LogoutAction
{
    public function execute(Request $request): void
    {
        $request->user()?->tokens()?->delete();
    }
}
