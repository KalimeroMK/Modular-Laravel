<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Actions;

use App\Modules\Auth\Http\Requests\SendResetLinkRequest;
use Illuminate\Support\Facades\Password;

class SendPasswordResetLinkAction
{
    public function execute(SendResetLinkRequest $request): string
    {
        return Password::sendResetLink($request->only('email'));
    }
}
