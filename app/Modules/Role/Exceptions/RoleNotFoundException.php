<?php

namespace App\Modules\Role\Exceptions;

use Exception;

class RoleNotFoundException extends Exception
{
    public function __construct(string $message = 'Role not found.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
