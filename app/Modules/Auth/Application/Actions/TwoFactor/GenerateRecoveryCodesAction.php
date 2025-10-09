<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Actions\TwoFactor;

use App\Modules\Auth\Application\DTO\TwoFactor\RecoveryCodesDTO;
use App\Modules\Auth\Application\Services\TwoFactor\ServiceInterface;
use App\Modules\User\Infrastructure\Models\User;
use Exception;

class GenerateRecoveryCodesAction
{
    public function __construct(
        protected ServiceInterface $twoFactorService,
    ) {}

    public function execute(User $user): RecoveryCodesDTO
    {
        if (!$this->twoFactorService->isTwoFactorEnabled($user)) {
            throw new Exception('Two-factor authentication is not enabled for this user.');
        }

        $recoveryCodes = $this->twoFactorService->generateRecoveryCodes();

        // Update user with new recovery codes
        $user->update([
            'two_factor_recovery_codes' => encrypt($recoveryCodes->toArray()),
        ]);

        return $recoveryCodes;
    }
}
