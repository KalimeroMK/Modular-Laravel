<?php

namespace App\Modules\Permission\Http\DTOs;

use Illuminate\Http\Request;

readonly class UpdatePermissionDTO
{
    public function __construct(
        public string $name,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = $request->validated();
        return new self(
            name: $data['name']
        );
    }

    public function toArray(): array
    {
        return ['name' => $this->name];
    }
}