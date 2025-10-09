<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\DTO;

readonly class SessionTokenDTO
{
    public function __construct(
        public string $token,
        public string $type = 'Bearer',
        public ?int $expiresIn = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'type' => $this->type,
            'expires_in' => $this->expiresIn,
        ];
    }
}
