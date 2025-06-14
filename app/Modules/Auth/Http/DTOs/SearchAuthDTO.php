<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\DTOs;

use Illuminate\Http\Request;

readonly class SearchAuthDTO
{
    public function __construct(
        public ?string $query = null
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->get('query')
        );
    }
}
