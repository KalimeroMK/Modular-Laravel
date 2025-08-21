<?php

declare(strict_types=1);

namespace App\Modules\Role\Http\DTOs;

use Illuminate\Http\Request;

readonly class RoleDTO
{
    public function __construct(
        public ?int $id,
        public string $name,
        /**
         * @var array<int, string>
         */
        public array $permissions = []
    ) {}

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        /** @var array{id?: int, name?: string, permissions?: array<int, string>} $validated */
        $validated = method_exists($request, 'validated') ? $request->validated() : $request->all();

        return new self(
            id: $id ?? $validated['id'] ?? null,
            name: $validated['name'] ?? '',
            permissions: $validated['permissions'] ?? []
        );
    }

    /**
     * @param  array{id?: int, name?: string, permissions?: array<int, string>}  $data
     */
    public static function fromArray(array $data): self
    {
        /** @var array{id?: int, name?: string, permissions?: array<int, string>} $data */
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? '',
            permissions: $data['permissions'] ?? []
        );
    }
}
