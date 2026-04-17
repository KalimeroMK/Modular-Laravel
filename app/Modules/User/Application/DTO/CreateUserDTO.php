<?php

declare(strict_types=1);

namespace App\Modules\User\Application\DTO;

use App\Modules\Core\Application\Contracts\DtoInterface;

readonly class CreateUserDTO implements DtoInterface
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?string $emailVerifiedAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            emailVerifiedAt: $data['email_verified_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'email_verified_at' => $this->emailVerifiedAt,
        ];
    }
}
