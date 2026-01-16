<?php

declare(strict_types=1);

namespace App\Modules\Auth\Infrastructure\Exceptions;

use App\Modules\Core\Exceptions\BaseException;

class TwoFactorAlreadyEnabledException extends BaseException
{
    protected int $statusCode = 400;

    protected string $errorCode = 'TWO_FACTOR_ALREADY_ENABLED';

    public function __construct(string $message = 'Two-factor authentication is already enabled for this user.')
    {
        parent::__construct($message);
    }
}
