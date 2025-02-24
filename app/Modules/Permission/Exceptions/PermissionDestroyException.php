<?php

namespace App\Modules\Permission\Exceptions;

use Exception;

class PermissionDestroyException extends Exception
{
    public function __construct(string $message = 'Failed to destroy Permission.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
