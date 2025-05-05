<?php

namespace App\Modules\User\Http\DTOs;

class SearchUserDTO
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
