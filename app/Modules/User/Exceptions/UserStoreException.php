<?php

declare(strict_types=1);

namespace App\Modules\User\Exceptions;

use Exception;

class UserStoreException extends Exception
{
    public function __construct(string $message = 'Failed to store User.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
