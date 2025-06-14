<?php

declare(strict_types=1);

namespace App\Modules\Core\Interfaces;

interface SearchInterface
{
    public function search(array $request): mixed;
}
