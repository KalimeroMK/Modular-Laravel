<?php

namespace App\Modules\Permission\Exceptions;

use Exception;

class PermissionUpdateException extends Exception
{
    public function __construct(string $message = 'Failed to update Permission.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
