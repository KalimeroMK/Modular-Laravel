<?php

declare(strict_types=1);

namespace App\Modules\User\Exceptions;

use Exception;

class UserIndexException extends Exception
{
    public function __construct(string $message = 'Failed to retrieve User list.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
