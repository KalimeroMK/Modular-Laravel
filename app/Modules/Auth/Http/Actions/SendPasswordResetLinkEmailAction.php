<?php

namespace App\Modules\Auth\Http\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class SendPasswordResetLinkEmailAction
{
    public function execute(Request $request): array
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return ['message' => __($status)];
        } else {
            throw ValidationException::withMessages([
                'email' => __($status),
            ]);
        }
    }
}
