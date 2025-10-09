<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\DTO;

readonly class UpdatePermissionDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $guardName = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            guardName: $data['guard_name'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'guard_name' => $this->guardName,
        ], fn ($value) => $value !== null);
    }
}
