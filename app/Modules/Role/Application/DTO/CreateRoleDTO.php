<?php

declare(strict_types=1);

namespace App\Modules\Role\Application\DTO;

readonly class CreateRoleDTO
{
    public function __construct(
        public string $name,
        public string $guardName = 'api',
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            guardName: $data['guard_name'] ?? 'api',
        );
    }
}
