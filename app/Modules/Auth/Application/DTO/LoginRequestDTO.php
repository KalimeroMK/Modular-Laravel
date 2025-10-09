<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\DTO;

readonly class LoginRequestDTO
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password']
        );
    }
}
