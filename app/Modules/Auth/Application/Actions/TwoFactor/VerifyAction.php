<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Actions\TwoFactor;

use App\Modules\Auth\Application\DTO\TwoFactor\VerificationDTO;
use App\Modules\Auth\Application\Services\TwoFactor\ServiceInterface;
use App\Modules\User\Infrastructure\Models\User;
use Exception;

class VerifyAction
{
    public function __construct(
        protected ServiceInterface $twoFactorService,
    ) {}

    public function execute(User $user, VerificationDTO $dto): bool
    {
        // Allow verification even if 2FA is not yet confirmed (for initial setup)
        if (! $user->two_factor_secret) {
            throw new Exception('Two-factor authentication secret is not set. Please run setup first.');
        }

        return $this->twoFactorService->verifyTwoFactor($user, $dto);
    }
}
