<?php

namespace App\Modules\Auth\Exceptions;

use Exception;

class AuthIndexException extends Exception
{
    public function __construct(string $message = 'Failed to retrieve Auth list.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
