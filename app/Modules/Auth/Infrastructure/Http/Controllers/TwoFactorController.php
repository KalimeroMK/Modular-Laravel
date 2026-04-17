<?php

declare(strict_types=1);

namespace App\Modules\Auth\Infrastructure\Http\Controllers;

use App\Modules\Auth\Application\Actions\TwoFactor\DisableAction;
use App\Modules\Auth\Application\Actions\TwoFactor\GenerateRecoveryCodesAction;
use App\Modules\Auth\Application\Actions\TwoFactor\GetStatusAction;
use App\Modules\Auth\Application\Actions\TwoFactor\SetupAction;
use App\Modules\Auth\Application\Actions\TwoFactor\VerifyAction;
use App\Modules\Auth\Application\DTO\TwoFactor\VerificationDTO;
use App\Modules\Auth\Infrastructure\Http\Requests\TwoFactorSetupRequest;
use App\Modules\Auth\Infrastructure\Http\Requests\TwoFactorVerifyRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function __construct(
        protected SetupAction $setupAction,
        protected VerifyAction $verifyAction,
        protected DisableAction $disableAction,
        protected GetStatusAction $getStatusAction,
        protected GenerateRecoveryCodesAction $generateRecoveryCodesAction,
    ) {}

    public function setup(TwoFactorSetupRequest $request): JsonResponse
    {
        $user = $request->user();
        if ($user === null) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $setupData = $this->setupAction->execute($user);

        return response()->json($setupData->toArray());
    }

    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user === null) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $status = $this->getStatusAction->execute($user);

        return response()->json($status);
    }

    public function verify(TwoFactorVerifyRequest $request): JsonResponse
    {
        $user = $request->user();
        if ($user === null) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $dto = VerificationDTO::fromArray($request->validated());

        $verified = $this->verifyAction->execute($user, $dto);

        return response()->json(['verified' => $verified]);
    }

    public function disable(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user === null) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $this->disableAction->execute($user);

        return response()->json(['message' => 'Two-factor authentication disabled successfully']);
    }

    public function generateRecoveryCodes(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user === null) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $recoveryCodes = $this->generateRecoveryCodesAction->execute($user);

        return response()->json($recoveryCodes->toArray());
    }
}
