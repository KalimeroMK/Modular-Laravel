<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\DTO\TwoFactor;

class VerificationDTO
{
    public function __construct(
        public string $code,
        public ?string $recoveryCode = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'] ?? '',
            recoveryCode: $data['recovery_code'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'recovery_code' => $this->recoveryCode,
        ];
    }
}
