<?php

declare(strict_types=1);

namespace App\Modules\Role\Application\DTO;

use App\Modules\Core\Application\Contracts\DtoInterface;

readonly class CreateRoleDTO implements DtoInterface
{
    public function __construct(
        public string $name,
        public string $guardName = 'api',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            guardName: $data['guard_name'] ?? 'api',
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'guard_name' => $this->guardName,
        ];
    }
}
