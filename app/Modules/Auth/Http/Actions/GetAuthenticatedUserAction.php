<?php

namespace App\Modules\Auth\Http\Actions;

use Illuminate\Http\Request;

class GetAuthenticatedUserAction
{
    public function execute(Request $request)
    {
        return $request->user();
    }
}
