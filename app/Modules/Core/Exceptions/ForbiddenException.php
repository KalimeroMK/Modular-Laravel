<?php

declare(strict_types=1);

namespace App\Modules\Core\Exceptions;

use Throwable;

class ForbiddenException extends BaseException
{
    protected int $statusCode = 403;

    protected string $errorCode = 'FORBIDDEN';

    public function __construct(string $message = 'Access forbidden', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
