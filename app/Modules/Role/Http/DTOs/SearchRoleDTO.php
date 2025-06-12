<?php

namespace App\Modules\Role\Http\DTOs;

use Illuminate\Http\Request;

readonly class SearchRoleDTO
{
    public function __construct(
        public ?string $name = null
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = $request->validated();

        return new self(
            name: $data['name'] ?? null
        );
    }
}
