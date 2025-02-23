<?php

namespace App\Modules\Auth\Exceptions;

use Exception;

class AuthNotFoundException extends Exception
{
    public function __construct(string $message = 'Auth not found.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
