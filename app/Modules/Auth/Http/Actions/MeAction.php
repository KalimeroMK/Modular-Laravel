<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Actions;

use Illuminate\Http\Request;

class MeAction
{
    public function execute(Request $request): mixed
    {
        return $request->user();
    }
}
