<?php

namespace App\Modules\User\Http\DTOs;

use Illuminate\Http\Request;

readonly class UpdateUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = $request->validated();

        return new self(
            name: $data['name'],
            email: $data['email'],
        );
    }
}
