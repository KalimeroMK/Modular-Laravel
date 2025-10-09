<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\DTO;

readonly class CreatePermissionDTO
{
    public function __construct(
        public string $name,
        public string $guardName = 'web',
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            guardName: $data['guard_name'] ?? 'web',
        );
    }
}
