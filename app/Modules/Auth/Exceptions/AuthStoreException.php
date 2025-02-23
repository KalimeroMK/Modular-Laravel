<?php

namespace App\Modules\Auth\Exceptions;

use Exception;

class AuthStoreException extends Exception
{
    public function __construct(string $message = 'Failed to store Auth.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
