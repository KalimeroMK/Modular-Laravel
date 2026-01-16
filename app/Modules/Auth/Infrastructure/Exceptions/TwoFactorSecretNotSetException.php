<?php

declare(strict_types=1);

namespace App\Modules\Auth\Infrastructure\Exceptions;

use App\Modules\Core\Exceptions\BaseException;

class TwoFactorSecretNotSetException extends BaseException
{
    protected int $statusCode = 400;

    protected string $errorCode = 'TWO_FACTOR_SECRET_NOT_SET';

    public function __construct(string $message = 'Two-factor authentication secret is not set. Please run setup first.')
    {
        parent::__construct($message);
    }
}
