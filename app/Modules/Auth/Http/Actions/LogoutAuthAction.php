<?php

namespace App\Modules\Auth\Http\Actions;

use Illuminate\Http\Request;

class LogoutAuthAction
{
    public function execute(Request $request): void
    {
        $request->user()->currentAccessToken()->delete();
    }
}
