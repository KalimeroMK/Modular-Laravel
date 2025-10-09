<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Services;

use App\Modules\Auth\Application\DTO\SessionTokenDTO;
use App\Modules\User\Infrastructure\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class IssueTokenService implements IssueTokenServiceInterface
{
    public function issueToken(User $user, string $tokenName = 'auth_token'): SessionTokenDTO
    {
        $token = $user->createToken($tokenName);

        return new SessionTokenDTO(
            token: $token->plainTextToken,
            type: 'Bearer',
            expiresIn: null // Sanctum tokens don't expire by default
        );
    }

    public function revokeToken(User $user): bool
    {
        /** @var PersonalAccessToken|null $token */
        $token = $user->currentAccessToken();

        if ($token !== null) {
            $token->delete();
        }

        return true;
    }

    public function revokeAllTokens(User $user): bool
    {
        $user->tokens()->delete();

        return true;
    }
}
