<?php

declare(strict_types=1);

namespace App\Modules\Permission\Http\DTOs;

use App\Modules\Permission\Models\Permission;
use Illuminate\Http\Request;

readonly class PermissionDTO
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $guard_name,
        public ?string $created_at,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?Permission $existing = null): self
    {
        $validated = $request->validated();

        return new self(
            id: $id,
            name: $validated['name'] ?? $existing?->name ?? '',
            guard_name: $validated['guard_name'] ?? $existing?->guard_name ?? 'web',
            created_at: $validated['created_at'] ?? $existing?->created_at?->toDateTimeString()
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'] ?? '',
            $data['guard_name'] ?? 'web',
            isset($data['created_at']) ? (string) $data['created_at'] : now()->toDateTimeString(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'guard_name' => $this->guard_name,
        ];
    }
}
