<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\DTO\TwoFactor;

class SetupDTO
{
    public function __construct(
        public string $secretKey,
        public string $qrCodeUrl,
        public string $recoveryCodes,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            secretKey: $data['secret_key'],
            qrCodeUrl: $data['qr_code_url'],
            recoveryCodes: $data['recovery_codes'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'secret_key' => $this->secretKey,
            'qr_code_url' => $this->qrCodeUrl,
            'recovery_codes' => $this->recoveryCodes,
        ];
    }
}
