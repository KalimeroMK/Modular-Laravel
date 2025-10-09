<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\DTO\TwoFactor;

class VerificationDTO
{
    public function __construct(
        public string $code,
        public ?string $recoveryCode = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'] ?? '',
            recoveryCode: $data['recovery_code'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'recovery_code' => $this->recoveryCode,
        ];
    }
}