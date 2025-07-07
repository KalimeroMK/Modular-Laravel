<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\DTOs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

readonly class UpdateAuthDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $password = null,
    ) {}

    public static function fromRequest(FormRequest $request): self
    {
        $data = $request->validated();

        return new self(
            $data['name'] ?? null,
            $data['email'] ?? null,
            $data['password'] ?? null,
        );
    }
}
