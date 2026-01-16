<?php

declare(strict_types=1);

namespace App\Modules\Auth\Infrastructure\Exceptions;

use App\Modules\Core\Exceptions\BaseException;

class TwoFactorInvalidCodeException extends BaseException
{
    protected int $statusCode = 400;

    protected string $errorCode = 'TWO_FACTOR_INVALID_CODE';

    public function __construct(string $message = 'Invalid two-factor authentication code.')
    {
        parent::__construct($message);
    }
}
