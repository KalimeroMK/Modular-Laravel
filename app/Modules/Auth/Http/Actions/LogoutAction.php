<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Actions;

use Illuminate\Http\Request;

class LogoutAction
{
    public function execute(Request $request): void
    {
        $request->user()?->tokens()?->delete();
    }
}
