<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\DTO;

use App\Modules\Permission\Infrastructure\Models\Permission;
use Carbon\Carbon;

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
        if ($permission->name === null) {
            $permission->refresh();
        }

        $formatDate = fn ($date) => $date instanceof Carbon ? $date->toISOString() : ($date ? (string) $date : null);

        return new self(
            id: (int) ($permission->id ?? $permission->getKey() ?? 0),
            name: $permission->name ?? '',
            guardName: $permission->guard_name ?? 'api',
            createdAt: $formatDate($permission->created_at),
            updatedAt: $formatDate($permission->updated_at),
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
