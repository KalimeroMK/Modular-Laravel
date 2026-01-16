<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Actions\TwoFactor;

use App\Modules\Auth\Application\DTO\TwoFactor\RecoveryCodesDTO;
use App\Modules\Auth\Application\Services\TwoFactor\ServiceInterface;
use App\Modules\Auth\Infrastructure\Exceptions\TwoFactorNotEnabledException;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Support\Facades\Crypt;

class GenerateRecoveryCodesAction
{
    public function __construct(
        protected ServiceInterface $twoFactorService,
    ) {}

    public function execute(User $user): RecoveryCodesDTO
    {
        if (! $this->twoFactorService->isTwoFactorEnabled($user)) {
            throw new TwoFactorNotEnabledException();
        }

        $recoveryCodes = $this->twoFactorService->generateRecoveryCodes();

        // Update user with new recovery codes
        $user->update([
            'two_factor_recovery_codes' => Crypt::encrypt($recoveryCodes->toArray()),
        ]);

        return $recoveryCodes;
    }
}
