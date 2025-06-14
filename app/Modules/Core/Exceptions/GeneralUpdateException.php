<?php

declare(strict_types=1);

namespace App\Modules\Core\Exceptions;

class GeneralUpdateException extends GeneralException
{
    public $code = 422;

    public function message(): ?string
    {
        return 'Error while updating resource in the database';
    }
}
