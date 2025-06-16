<?php

declare(strict_types=1);

namespace App\Modules\Permission\Exceptions;

use Exception;

class PermissionNotFoundException extends Exception
{
    public function __construct(string $message = 'Permission not found.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
