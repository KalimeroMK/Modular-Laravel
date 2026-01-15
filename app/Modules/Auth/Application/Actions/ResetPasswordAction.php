<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Actions;

use App\Modules\Auth\Infrastructure\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Password;

class ResetPasswordAction
{
    public function execute(ResetPasswordRequest $request): string
    {

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password): void {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return 'passwords.reset';
        }

        // Return error message instead of throwing exception
        // This allows the controller to handle validation errors properly
        $validator = \Illuminate\Support\Facades\Validator::make(
            ['password' => ''],
            ['password' => ['required']],
            ['password.required' => __($status)]
        );
        throw new \Illuminate\Validation\ValidationException($validator);
    }
}
