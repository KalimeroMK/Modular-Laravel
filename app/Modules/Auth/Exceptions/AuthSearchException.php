<?php

namespace App\Modules\Auth\Exceptions;

use Exception;

class AuthSearchException extends Exception
{
    public function __construct(string $message = 'Failed to search Auth.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
