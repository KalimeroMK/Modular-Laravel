<?php

namespace App\Modules\Permission\Exceptions;

use Exception;

class PermissionIndexException extends Exception
{
    public function __construct(string $message = 'Failed to retrieve Permission list.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
