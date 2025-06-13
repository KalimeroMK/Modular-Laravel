<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Http\Actions\LoginAction;
use App\Modules\Auth\Http\Actions\LogoutAction;
use App\Modules\Auth\Http\Actions\MeAction;
use App\Modules\Auth\Http\Actions\RegisterAction;
use App\Modules\Auth\Http\Actions\ResetPasswordAction;
use App\Modules\Auth\Http\Actions\SendPasswordResetLinkAction;
use App\Modules\Auth\Http\DTOs\LoginDTO;
use App\Modules\Auth\Http\DTOs\RegisterDTO;
use App\Modules\Auth\Http\Requests\LoginRequest;
use App\Modules\Auth\Http\Requests\RegisterRequest;
use App\Modules\Auth\Http\Requests\ResetPasswordRequest;
use App\Modules\Auth\Http\Requests\SendResetLinkRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(LoginRequest $request, LoginAction $action): JsonResponse
    {
        $data = $action->execute(LoginDTO::fromRequest($request));

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function register(RegisterRequest $request, RegisterAction $action): JsonResponse
    {
        $data = $action->execute(RegisterDTO::fromRequest($request));

        return response()->json(['status' => 'success', 'data' => ['user' => $data['user'], 'token' => $data['token']]]);
    }

    public function logout(Request $request, LogoutAction $action): JsonResponse
    {
        $action->execute($request);

        return response()->json(['status' => 'success']);
    }

    public function me(Request $request, MeAction $action): JsonResponse
    {
        $user = $action->execute($request);

        return response()->json(['status' => 'success', 'data' => $user]);
    }

    public function sendResetLink(SendResetLinkRequest $request, SendPasswordResetLinkAction $action): JsonResponse
    {
        $status = $action->execute($request);

        return response()->json(['status' => 'success', 'message' => __($status)]);
    }

    public function resetPassword(ResetPasswordRequest $request, ResetPasswordAction $action): JsonResponse
    {
        $status = $action->execute($request);

        return response()->json(['status' => 'success', 'message' => __($status)]);
    }
}
