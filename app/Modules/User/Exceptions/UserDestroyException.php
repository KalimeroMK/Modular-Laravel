<?php

namespace App\Modules\User\Exceptions;

use Exception;

class UserDestroyException extends Exception
{
    public function __construct(string $message = 'Failed to destroy User.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
