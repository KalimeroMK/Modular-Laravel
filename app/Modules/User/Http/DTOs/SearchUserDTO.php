<?php

namespace App\Modules\User\Http\DTOs;

use Illuminate\Http\Request;

readonly class SearchUserDTO
{
    public function __construct(
        public ?string $search = null
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            search: $request->get('search')
        );
    }
}
