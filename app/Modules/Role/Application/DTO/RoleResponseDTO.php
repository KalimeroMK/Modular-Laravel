<?php

declare(strict_types=1);

namespace App\Modules\Role\Application\DTO;

use App\Modules\Role\Infrastructure\Models\Role;
use Carbon\Carbon;

readonly class RoleResponseDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $guardName,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
    ) {}

    public static function fromRole(Role $role): self
    {
        if ($role->name === null) {
            $role->refresh();
        }

        $formatDate = fn ($date) => $date instanceof Carbon ? $date->toISOString() : ($date ? (string) $date : null);

        return new self(
            id: (int) ($role->id ?? $role->getKey() ?? 0),
            name: $role->name ?? '',
            guardName: $role->guard_name ?? 'api',
            createdAt: $formatDate($role->created_at),
            updatedAt: $formatDate($role->updated_at),
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
            'guard_name' => $this->guardName,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
