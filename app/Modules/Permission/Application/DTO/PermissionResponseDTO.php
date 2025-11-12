<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\DTO;

use App\Modules\Permission\Infrastructure\Models\Permission;

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
        // Ensure the model is fresh from database if attributes are null
        if ($permission->name === null) {
            $permission->refresh();
        }

        return new self(
            id: (int) ($permission->id ?? $permission->getKey() ?? 0),
            name: $permission->name ?? '',
            guardName: $permission->guard_name ?? 'web',
            createdAt: $permission->created_at instanceof \Carbon\Carbon ? $permission->created_at->toISOString() : ($permission->created_at ? (string) $permission->created_at : null),
            updatedAt: $permission->updated_at instanceof \Carbon\Carbon ? $permission->updated_at->toISOString() : ($permission->updated_at ? (string) $permission->updated_at : null),
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
