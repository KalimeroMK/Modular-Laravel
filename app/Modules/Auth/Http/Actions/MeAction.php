<?php

namespace App\Modules\Auth\Http\Actions;

use Illuminate\Http\Request;

class MeAction
{
    public function execute(Request $request): mixed
    {
        return $request->user();
    }
}
