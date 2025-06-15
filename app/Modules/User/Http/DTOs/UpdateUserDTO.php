<?php

declare(strict_types=1);

namespace App\Modules\User\Http\DTOs;

use Illuminate\Http\Request;

readonly class UpdateUserDTO
{
    public function __construct(
        public ?string $name,
        public ?string $email,
        public ?string $password,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromRequest(Request $request): self
    {
        $data = $request->all();

        return new self(
            $data['name'] ?? null,
            $data['email'] ?? null,
            $data['password'] ?? null
        );
    }
}
