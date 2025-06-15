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

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromRequest(Request $request): self
    {
        $data = $request->all();

        return new self(
            $data['name'],
            $data['email'],
            bcrypt($data['password'])
        );
    }
}
