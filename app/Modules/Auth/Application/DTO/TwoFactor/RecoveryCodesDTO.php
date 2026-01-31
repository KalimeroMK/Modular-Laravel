<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\DTO\TwoFactor;

class RecoveryCodesDTO
{
    


    public function __construct(
        public array $codes,
    ) {}

    


    public static function fromArray(array $data): self
    {
        return new self(
            codes: $data['codes'],
        );
    }

    


    public function toArray(): array
    {
        return [
            'codes' => $this->codes,
        ];
    }
}
