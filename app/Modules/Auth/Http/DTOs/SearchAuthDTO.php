<?php

namespace App\Modules\Auth\Http\DTOs;

class SearchAuthDTO
{
    public string $query;

    public function __construct(string $query)
    {
        $this->query = $query;
    }

    public static function fromArray(array $data): self
    {
        return new self($data['query'] ?? '');
    }

    public function toArray(): array
    {
        return ['query' => $this->query];
    }
}
