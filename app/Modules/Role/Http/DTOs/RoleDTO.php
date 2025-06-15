<?php

declare(strict_types=1);

namespace App\Modules\Role\Http\DTOs;

use Illuminate\Http\Request;

readonly class RoleDTO
{
    /**
     * @param  array<int, mixed>  $permissions
     */
    public function __construct(
        public ?int $id,
        public string $name,
        public array $permissions = []
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = $request->all();

        return self::fromArray($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? '',
            permissions: $data['permissions'] ?? []
        );
    }
}
