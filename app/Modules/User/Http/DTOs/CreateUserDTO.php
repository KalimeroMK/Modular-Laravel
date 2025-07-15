<?php

declare(strict_types=1);

namespace App\Modules\User\Http\DTOs;

use Illuminate\Http\Request;

readonly class CreateUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}

    public static function fromRequest(Request $request): self
    {
        /** @var array{name: string, email: string, password: string} $data */
        $data = method_exists($request, 'validated') ? $request->validated() : $request->all();

        return new self(
            name: $data['name'],
            email: $data['email'],
            password: bcrypt($data['password']),
        );
    }
}
