<?php

namespace App\Modules\Core\Interfaces;

interface SearchInterface
{
    public function search(array $request): mixed;
}
