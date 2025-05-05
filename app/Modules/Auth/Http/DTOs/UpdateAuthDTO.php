<?php

namespace App\Modules\Auth\Http\DTOs;

class UpdateAuthDTO
{
    public string $email;

    public ?string $name;

    public ?string $password;

    public function __construct(string $email, ?string $name = null, ?string $password = null)
    {
        $this->email = $email;
        $this->name = $name;
        $this->password = $password;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['email'] ?? '',
            $data['name'] ?? null,
            $data['password'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'email' => $this->email,
            'name' => $this->name,
            'password' => $this->password,
        ], fn ($v) => ! is_null($v));
    }
}
