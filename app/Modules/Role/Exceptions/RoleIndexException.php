<?php

namespace App\Modules\Role\Exceptions;

use Exception;

class RoleIndexException extends Exception
{
    public function __construct(string $message = 'Failed to retrieve Role list.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
