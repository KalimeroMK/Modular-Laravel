<?php

declare(strict_types=1);

namespace App\Modules\Auth\Infrastructure\Exceptions;

use App\Modules\Core\Exceptions\BaseException;

class TwoFactorNotEnabledException extends BaseException
{
    protected int $statusCode = 400;

    protected string $errorCode = 'TWO_FACTOR_NOT_ENABLED';

    public function __construct(string $message = 'Two-factor authentication is not enabled for this user.')
    {
        parent::__construct($message);
    }
}
