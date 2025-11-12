<?php

declare(strict_types=1);

namespace App\Modules\Core\Exceptions;

use Throwable;

class NotFoundException extends BaseException
{
    protected int $statusCode = 404;

    protected string $errorCode = 'RESOURCE_NOT_FOUND';

    public function __construct(string $message = 'Resource not found', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
