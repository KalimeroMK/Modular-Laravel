<?php

namespace App\Modules\Auth\Http\Actions;

use Illuminate\Support\Facades\Password;
use App\Modules\Auth\Http\Requests\SendResetLinkRequest;

class SendPasswordResetLinkAction
{
    public function execute(SendResetLinkRequest $request): string
    {
        return Password::sendResetLink($request->only('email'));
    }
}
