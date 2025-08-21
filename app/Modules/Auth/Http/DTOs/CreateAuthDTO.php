<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\DTOs;

use Illuminate\Foundation\Http\FormRequest;

readonly class CreateAuthDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}

    public static function fromRequest(FormRequest $request): self
    {
        $data = $request->validated();

        return new self(
            $data['name'],
            $data['email'],
            $data['password']
        );
    }
}
