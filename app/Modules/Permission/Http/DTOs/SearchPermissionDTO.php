<?php

namespace App\Modules\Permission\Http\DTOs;

use Illuminate\Http\Request;

readonly class SearchPermissionDTO
{
    public function __construct(
        public ?string $name = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = $request->validated();
        return new self(
            name: $data['name'] ?? null
        );
    }

    public function toArray(): array
    {
        return ['name' => $this->name];
    }
}