<?php

namespace App\Modules\Auth\Http\DTOs;

use Illuminate\Http\Request;

readonly class CreateAuthDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = $request->validated();
        return new self(
            $data['name'],
            $data['email'],
            $data['password']
        );
    }
}
