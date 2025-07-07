<?php

declare(strict_types=1);

namespace App\Modules\Core\Exceptions;

class GeneralDestroyException extends GeneralException
{
    /**
     * @var int
     */
    protected $code = 422;

    public function message(): ?string
    {
        return 'Error while deleting resource';
    }
}
