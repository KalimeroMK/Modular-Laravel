<?php

declare(strict_types=1);

namespace App\Modules\Auth\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Application\Actions\GetCurrentUserAction;
use App\Modules\Auth\Application\Actions\LoginAction;
use App\Modules\Auth\Application\Actions\LogoutAction;
use App\Modules\Auth\Application\Actions\RegisterUserAction;
use App\Modules\Auth\Application\Actions\ResetPasswordAction;
use App\Modules\Auth\Application\Actions\SendPasswordResetLinkAction;
use App\Modules\Auth\Application\DTO\LoginRequestDTO;
use App\Modules\Auth\Application\DTO\RegisterUserDTO;
use App\Modules\Auth\Infrastructure\Http\Requests\LoginRequest;
use App\Modules\Auth\Infrastructure\Http\Requests\RegisterRequest;
use App\Modules\Auth\Infrastructure\Http\Requests\ResetPasswordRequest;
use App\Modules\Auth\Infrastructure\Http\Requests\SendPasswordResetLinkRequest;
use App\Modules\Core\Traits\SwaggerTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use SwaggerTrait;

    public function __construct(
        protected LoginAction $loginAction,
        protected RegisterUserAction $registerAction,
        protected LogoutAction $logoutAction,
        protected GetCurrentUserAction $getCurrentUserAction,
        protected SendPasswordResetLinkAction $sendResetLinkAction,
        protected ResetPasswordAction $resetPasswordAction,
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $dto = LoginRequestDTO::fromArray($request->validated());
        $data = $this->loginAction->execute($dto);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = RegisterUserDTO::fromArray($request->validated());
        $data = $this->registerAction->execute($dto);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->logoutAction->execute($request);

        return response()->json(['status' => 'success']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->getCurrentUserAction->execute($request);

        return response()->json([
            'status' => 'success',
            'data' => $user->toArray(),
        ]);
    }

    public function sendResetLink(SendPasswordResetLinkRequest $request): JsonResponse
    {
        $status = $this->sendResetLinkAction->execute($request);

        return response()->json(['status' => 'success', 'message' => __($status)]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = $this->resetPasswordAction->execute($request);

        return response()->json(['status' => 'success', 'message' => __($status)]);
    }
}
