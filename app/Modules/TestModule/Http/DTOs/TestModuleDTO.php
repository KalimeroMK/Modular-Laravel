<?php

namespace App\Modules\TestModule\Http\DTOs;

use Illuminate\Http\Request;

readonly class TestModuleDTO
{
    public function __construct(
        public ?string $name,
        public ?string $description,
        public ?bool $is_active
    ) {}

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        $data = $request->validated();
        return new self(
            $data['name'] ?? null,
            $data['description'] ?? null,
            $data['is_active'] ?? null
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'] ?? null,
            $data['description'] ?? null,
            $data['is_active'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active
        ];
    }
}
