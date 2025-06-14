<?php

declare(strict_types=1);

namespace App\Modules\Core\Exceptions;

class FormRequestTableNotFoundException extends GeneralException
{
    public $code = 404;

    public function message(): ?string
    {
        return 'Table not found in the form request';
    }
}
