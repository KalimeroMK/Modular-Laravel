<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\DTO;

use App\Modules\Core\Application\Contracts\DtoInterface;

readonly class UpdatePermissionDTO implements DtoInterface
{
    public function __construct(
        public ?string $name = null,
        public ?string $guardName = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            guardName: $data['guard_name'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'guard_name' => $this->guardName,
        ], fn ($value) => $value !== null);
    }
}
