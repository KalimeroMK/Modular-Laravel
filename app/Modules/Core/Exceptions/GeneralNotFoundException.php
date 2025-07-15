<?php

declare(strict_types=1);

namespace App\Modules\Core\Exceptions;

class GeneralNotFoundException extends GeneralException
{
    /**
     * @var int
     */
    protected $code = 404;

    public function message(): ?string
    {
        return 'The requested resource was not found in the database';
    }
}
