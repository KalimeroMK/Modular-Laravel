<?php

namespace App\Modules\User\Http\DTOs;

class UpdateUserDTO
{
    public string $name;

    public string $email;

    public ?string $password;

    public function __construct(string $name, string $email, ?string $password = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
