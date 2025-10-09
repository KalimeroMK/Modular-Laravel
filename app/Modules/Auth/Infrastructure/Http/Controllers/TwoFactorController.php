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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Two-Factor Authentication', description: '2FA management endpoints')]
class TwoFactorController
{
    public function __construct(
        protected SetupAction $setupAction,
        protected VerifyAction $verifyAction,
        protected DisableAction $disableAction,
        protected GetStatusAction $getStatusAction,
        protected GenerateRecoveryCodesAction $generateRecoveryCodesAction,
    ) {}

    #[OA\Get(
        path: '/api/v1/auth/2fa/status',
        summary: 'Get 2FA status',
        description: 'Get the current two-factor authentication status for the authenticated user',
        tags: ['Two-Factor Authentication'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: '2FA status retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'enabled', type: 'boolean', example: true),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $status = $this->getStatusAction->execute($user);

        return response()->json($status);
    }

    #[OA\Post(
        path: '/api/v1/auth/2fa/setup',
        summary: 'Setup 2FA',
        description: 'Generate secret key and QR code for two-factor authentication setup',
        tags: ['Two-Factor Authentication'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: '2FA setup data generated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'secret_key', type: 'string', example: 'JBSWY3DPEHPK3PXP'),
                        new OA\Property(property: 'qr_code_url', type: 'string', example: 'https://api.qrserver.com/v1/create-qr-code/?data=otpauth://totp/...'),
                        new OA\Property(property: 'recovery_codes', type: 'string', example: 'code1,code2,code3'),
                    ]
                )
            ),
            new OA\Response(response: 400, description: '2FA already enabled'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function setup(TwoFactorSetupRequest $request): JsonResponse
    {
        $user = $request->user();
        $setupData = $this->setupAction->execute($user);

        return response()->json($setupData->toArray());
    }

    #[OA\Post(
        path: '/api/v1/auth/2fa/verify',
        summary: 'Verify 2FA code',
        description: 'Verify a two-factor authentication code or recovery code',
        tags: ['Two-Factor Authentication'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['code'],
                properties: [
                    new OA\Property(property: 'code', type: 'string', example: '123456', description: '6-digit TOTP code'),
                    new OA\Property(property: 'recovery_code', type: 'string', example: 'abcd1234ef', description: '10-character recovery code'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: '2FA code verified successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'verified', type: 'boolean', example: true),
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Invalid code'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function verify(TwoFactorVerifyRequest $request): JsonResponse
    {
        $user = $request->user();
        $dto = VerificationDTO::fromArray($request->validated());
        
        $verified = $this->verifyAction->execute($user, $dto);

        return response()->json(['verified' => $verified]);
    }

    #[OA\Delete(
        path: '/api/v1/auth/2fa/disable',
        summary: 'Disable 2FA',
        description: 'Disable two-factor authentication for the authenticated user',
        tags: ['Two-Factor Authentication'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: '2FA disabled successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Two-factor authentication disabled successfully'),
                    ]
                )
            ),
            new OA\Response(response: 400, description: '2FA not enabled'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function disable(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->disableAction->execute($user);

        return response()->json(['message' => 'Two-factor authentication disabled successfully']);
    }

    #[OA\Post(
        path: '/api/v1/auth/2fa/recovery-codes',
        summary: 'Generate new recovery codes',
        description: 'Generate new recovery codes for two-factor authentication',
        tags: ['Two-Factor Authentication'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'New recovery codes generated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'codes', type: 'array', items: new OA\Items(type: 'string'), example: ['code1', 'code2', 'code3']),
                    ]
                )
            ),
            new OA\Response(response: 400, description: '2FA not enabled'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function generateRecoveryCodes(Request $request): JsonResponse
    {
        $user = $request->user();
        $recoveryCodes = $this->generateRecoveryCodesAction->execute($user);

        return response()->json($recoveryCodes->toArray());
    }
}
