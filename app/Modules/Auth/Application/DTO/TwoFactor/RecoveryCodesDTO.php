<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\DTO\TwoFactor;

class RecoveryCodesDTO
{
    /**
     * @param  array<int, string>  $codes
     */
    public function __construct(
        public array $codes,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            codes: $data['codes'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'codes' => $this->codes,
        ];
    }
}
