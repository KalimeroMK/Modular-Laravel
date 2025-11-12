<?php

declare(strict_types=1);

namespace App\Modules\Core\Exceptions;

use Throwable;

class UnauthorizedException extends BaseException
{
    protected int $statusCode = 401;

    protected string $errorCode = 'UNAUTHORIZED';

    public function __construct(string $message = 'Unauthorized access', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
