<?php

namespace App\Modules\Role\Http\DTOs;

class CreateRoleDTO
{
    public string $name;

    public string $description;

    public function __construct(string $name, string $description)
    {
        $this->name = $name;
        $this->description = $description;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'] ?? '',
            $data['description'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
