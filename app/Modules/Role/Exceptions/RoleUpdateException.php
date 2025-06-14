<?php

declare(strict_types=1);

namespace App\Modules\Role\Exceptions;

use Exception;

class RoleUpdateException extends Exception
{
    public function __construct(string $message = 'Failed to update Role.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
