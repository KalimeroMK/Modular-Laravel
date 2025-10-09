<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Services\TwoFactor;

use App\Modules\Auth\Application\DTO\TwoFactor\RecoveryCodesDTO;
use App\Modules\Auth\Application\DTO\TwoFactor\SetupDTO;
use App\Modules\Auth\Application\DTO\TwoFactor\VerificationDTO;
use App\Modules\User\Infrastructure\Models\User;

interface ServiceInterface
{
    public function generateSecretKey(): string;

    public function generateQrCodeUrl(User $user, string $secretKey): string;

    public function generateRecoveryCodes(): RecoveryCodesDTO;

    public function setupTwoFactor(User $user): SetupDTO;

    public function verifyTwoFactor(User $user, VerificationDTO $dto): bool;

    public function disableTwoFactor(User $user): bool;

    public function isTwoFactorEnabled(User $user): bool;

    public function verifyRecoveryCode(User $user, string $recoveryCode): bool;
}
