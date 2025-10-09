<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Services;

use App\Modules\Auth\Application\DTO\SessionTokenDTO;
use App\Modules\User\Infrastructure\Models\User;

interface IssueTokenServiceInterface
{
    public function issueToken(User $user, string $tokenName = 'auth_token'): SessionTokenDTO;

    public function revokeToken(User $user): bool;

    public function revokeAllTokens(User $user): bool;
}
