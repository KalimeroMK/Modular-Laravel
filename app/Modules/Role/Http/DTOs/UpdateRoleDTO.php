<?php

namespace App\Modules\Role\Http\DTOs;

use Illuminate\Http\Request;

readonly class UpdateRoleDTO
{
    public function __construct(
        public string $name
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = $request->validated();

        return new self(
            name: $data['name']
        );
    }
}
