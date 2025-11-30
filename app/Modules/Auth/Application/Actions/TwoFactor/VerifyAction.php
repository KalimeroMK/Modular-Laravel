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
        if (! $this->twoFactorService->isTwoFactorEnabled($user)) {
            throw new Exception('Two-factor authentication is not enabled for this user.');
        }

        return $this->twoFactorService->verifyTwoFactor($user, $dto);
    }
}
