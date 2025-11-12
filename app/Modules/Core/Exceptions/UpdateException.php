<?php

declare(strict_types=1);

namespace App\Modules\Core\Exceptions;

use Throwable;

class UpdateException extends BaseException
{
    protected int $statusCode = 500;

    protected string $errorCode = 'UPDATE_FAILED';

    public function __construct(string $message = 'Failed to update resource', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
