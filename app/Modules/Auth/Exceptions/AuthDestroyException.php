<?php

namespace App\Modules\Auth\Exceptions;

use Exception;

class AuthDestroyException extends Exception
{
    public function __construct(string $message = 'Failed to destroy Auth.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
