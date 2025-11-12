<?php

declare(strict_types=1);

namespace App\Modules\Core\Exceptions;

use Throwable;

class ValidationException extends BaseException
{
    protected int $statusCode = 422;

    protected string $errorCode = 'VALIDATION_ERROR';

    /**
     * @param  array<string, array<int, string>>  $errors
     */
    public function __construct(
        string $message = 'Validation failed',
        protected array $errors = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function getErrors(): array
    {
        return $this->errors;
    }
}
