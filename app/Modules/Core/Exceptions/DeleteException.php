<?php

declare(strict_types=1);

namespace App\Modules\Core\Exceptions;

use Throwable;

class DeleteException extends BaseException
{
    protected int $statusCode = 500;

    protected string $errorCode = 'DELETE_FAILED';

    public function __construct(string $message = 'Failed to delete resource', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
