<?php

namespace App\Modules\Role\Exceptions;

use Exception;

class RoleDestroyException extends Exception
{
    public function __construct(string $message = 'Failed to destroy Role.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
