<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\DTO;

use App\Modules\User\Infrastructure\Models\User;

readonly class UserResponseDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?string $emailVerifiedAt = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
    ) {}

    public static function fromUser(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            emailVerifiedAt: $user->email_verified_at instanceof \Carbon\Carbon ? $user->email_verified_at->toISOString() : $user->email_verified_at,
            createdAt: $user->created_at instanceof \Carbon\Carbon ? $user->created_at->toISOString() : $user->created_at,
            updatedAt: $user->updated_at instanceof \Carbon\Carbon ? $user->updated_at->toISOString() : $user->updated_at,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->emailVerifiedAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
