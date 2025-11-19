<?php

declare(strict_types=1);

namespace App\Modules\User\Application\DTO;

use App\Modules\User\Infrastructure\Models\User;
use Carbon\Carbon;

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
        if ($user->id === null) {
            $user->refresh();
        }

        $formatDate = fn ($date) => $date instanceof Carbon ? $date->toISOString() : ($date ? (string) $date : null);

        return new self(
            id: $user->id ?? 0,
            name: $user->name ?? '',
            email: $user->email ?? '',
            emailVerifiedAt: $formatDate($user->email_verified_at),
            createdAt: $formatDate($user->created_at),
            updatedAt: $formatDate($user->updated_at),
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
