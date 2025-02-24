<?php

namespace App\Modules\Role\Exceptions;

use Exception;

class RoleSearchException extends Exception
{
    public function __construct(string $message = 'Failed to search Role.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
