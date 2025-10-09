<?php

declare(strict_types=1);

namespace App\Modules\Role\Application\DTO;

use App\Modules\Role\Infrastructure\Models\Role;

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
        return new self(
            id: (int) $role->id,
            name: $role->name,
            guardName: $role->guard_name ?? 'web',
            createdAt: $role->created_at instanceof \Carbon\Carbon ? $role->created_at->toISOString() : $role->created_at,
            updatedAt: $role->updated_at instanceof \Carbon\Carbon ? $role->updated_at->toISOString() : $role->updated_at,
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
