<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Services\TwoFactor;

use App\Modules\Auth\Application\DTO\TwoFactor\RecoveryCodesDTO;
use App\Modules\Auth\Application\DTO\TwoFactor\SetupDTO;
use App\Modules\Auth\Application\DTO\TwoFactor\VerificationDTO;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class Service implements ServiceInterface
{
    public function __construct(
        private Google2FA $google2fa,
    ) {}

    public function generateSecretKey(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    public function generateQrCodeUrl(User $user, string $secretKey): string
    {
        $companyName = config('app.name', 'Laravel App');
        $companyEmail = $user->email;

        return $this->google2fa->getQRCodeUrl(
            $companyName,
            $companyEmail,
            $secretKey
        );
    }

    public function generateRecoveryCodes(): RecoveryCodesDTO
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::random(10);
        }

        return new RecoveryCodesDTO($codes);
    }

    public function setupTwoFactor(User $user): SetupDTO
    {
        $secretKey = $this->generateSecretKey();
        $qrCodeUrl = $this->generateQrCodeUrl($user, $secretKey);
        $recoveryCodes = $this->generateRecoveryCodes();

        // Store encrypted secret key and recovery codes
        $user->update([
            'two_factor_secret' => Crypt::encrypt($secretKey),
            'two_factor_recovery_codes' => Crypt::encrypt($recoveryCodes->toArray()),
        ]);

        return new SetupDTO(
            secretKey: $secretKey,
            qrCodeUrl: $qrCodeUrl,
            recoveryCodes: implode(',', $recoveryCodes->codes),
        );
    }

    public function verifyTwoFactor(User $user, VerificationDTO $dto): bool
    {
        if (!$user->two_factor_secret) {
            return false;
        }

        $secretKey = Crypt::decrypt($user->two_factor_secret);

        // Check if it's a recovery code
        if ($dto->recoveryCode) {
            return $this->verifyRecoveryCode($user, $dto->recoveryCode);
        }

        // Verify TOTP code
        return $this->google2fa->verifyKey($secretKey, $dto->code);
    }

    public function disableTwoFactor(User $user): bool
    {
        $user->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        return true;
    }

    public function isTwoFactorEnabled(User $user): bool
    {
        return !empty($user->two_factor_secret);
    }

    public function verifyRecoveryCode(User $user, string $recoveryCode): bool
    {
        if (!$user->two_factor_recovery_codes) {
            return false;
        }

        $recoveryCodes = Crypt::decrypt($user->two_factor_recovery_codes);
        $codes = $recoveryCodes['codes'] ?? [];

        $key = array_search($recoveryCode, $codes, true);
        if ($key !== false) {
            // Remove used recovery code
            unset($codes[$key]);
            $codes = array_values($codes); // Re-index array

            $user->update([
                'two_factor_recovery_codes' => Crypt::encrypt(['codes' => $codes]),
            ]);

            return true;
        }

        return false;
    }
}
