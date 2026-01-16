<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Actions\TwoFactor;

use App\Modules\Auth\Application\DTO\TwoFactor\VerificationDTO;
use App\Modules\Auth\Application\Services\TwoFactor\ServiceInterface;
use App\Modules\Auth\Infrastructure\Exceptions\TwoFactorInvalidCodeException;
use App\Modules\Auth\Infrastructure\Exceptions\TwoFactorSecretNotSetException;
use App\Modules\User\Infrastructure\Models\User;

class VerifyAction
{
    public function __construct(
        protected ServiceInterface $twoFactorService,
    ) {}

    public function execute(User $user, VerificationDTO $dto): bool
    {
        // Allow verification even if 2FA is not yet confirmed (for initial setup)
        if (empty($user->two_factor_secret)) {
            throw new TwoFactorSecretNotSetException();
        }

        $verified = $this->twoFactorService->verifyTwoFactor($user, $dto);

        if (! $verified) {
            throw new TwoFactorInvalidCodeException();
        }

        return $verified;
    }
}
