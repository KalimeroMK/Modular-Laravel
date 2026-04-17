<?php

declare(strict_types=1);

namespace App\Modules\User\Application\DTO;

use App\Modules\Core\Application\Contracts\DtoInterface;

readonly class UpdateUserDTO implements DtoInterface
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $password = null,
        public ?string $emailVerifiedAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            password: $data['password'] ?? null,
            emailVerifiedAt: $data['email_verified_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'email_verified_at' => $this->emailVerifiedAt,
        ], fn ($value) => $value !== null);
    }
}
