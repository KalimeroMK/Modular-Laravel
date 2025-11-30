<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Actions;

use App\Modules\Auth\Infrastructure\Http\Requests\SendPasswordResetLinkRequest;
use Exception;
use Illuminate\Support\Facades\Password;

class SendPasswordResetLinkAction
{
    public function execute(SendPasswordResetLinkRequest $request): string
    {

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return 'passwords.sent';
        }

        throw new Exception('Failed to send reset link');
    }
}
