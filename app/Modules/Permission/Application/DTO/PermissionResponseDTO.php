<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\DTO;

use Spatie\Permission\Models\Permission;

readonly class PermissionResponseDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $guardName,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
    ) {}

    public static function fromPermission(Permission $permission): self
    {
        return new self(
            id: (int) $permission->id,
            name: $permission->name,
            guardName: $permission->guard_name ?? 'web',
            createdAt: $permission->created_at instanceof \Carbon\Carbon ? $permission->created_at->toISOString() : $permission->created_at,
            updatedAt: $permission->updated_at instanceof \Carbon\Carbon ? $permission->updated_at->toISOString() : $permission->updated_at,
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
