<?php

declare(strict_types=1);

namespace App\Modules\Core\Application\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface DtoInterface extends Arrayable
{
    public static function fromArray(array $data): self;
}
