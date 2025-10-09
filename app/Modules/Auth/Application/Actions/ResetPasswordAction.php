<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Actions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordAction
{
    public function execute(Request $request): string
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return 'passwords.reset';
        }

        throw new Exception('Failed to reset password');
    }
}
