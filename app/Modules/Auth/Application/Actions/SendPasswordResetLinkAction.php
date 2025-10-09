<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Actions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class SendPasswordResetLinkAction
{
    public function execute(Request $request): string
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return 'passwords.sent';
        }

        throw new Exception('Failed to send reset link');
    }
}
