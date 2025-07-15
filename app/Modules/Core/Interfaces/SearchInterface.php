<?php

declare(strict_types=1);

namespace App\Modules\Core\Interfaces;

interface SearchInterface
{
    /**
     * @param array<string, mixed> $request
     */
    public function search(array $request): mixed;
}
