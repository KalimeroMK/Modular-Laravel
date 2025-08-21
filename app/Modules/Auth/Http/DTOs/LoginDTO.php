<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\DTOs;

use Illuminate\Foundation\Http\FormRequest;

readonly class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}

    public static function fromRequest(FormRequest $request): self
    {
        $data = $request->validated();

        return new self(
            $data['email'],
            $data['password']
        );
    }
}
