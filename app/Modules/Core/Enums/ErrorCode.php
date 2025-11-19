<?php

declare(strict_types=1);

namespace App\Modules\Core\Enums;

enum ErrorCode: string
{
    case RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    case VALIDATION_ERROR = 'VALIDATION_ERROR';
    case UNAUTHORIZED = 'UNAUTHORIZED';
    case FORBIDDEN = 'FORBIDDEN';
    case CREATE_FAILED = 'CREATE_FAILED';
    case UPDATE_FAILED = 'UPDATE_FAILED';
    case DELETE_FAILED = 'DELETE_FAILED';
    case INTERNAL_ERROR = 'INTERNAL_ERROR';
    case ERROR = 'ERROR';

    /**
     * Get error message for the error code.
     */
    public function message(): string
    {
        return match ($this) {
            self::RESOURCE_NOT_FOUND => 'Resource not found',
            self::VALIDATION_ERROR => 'Validation failed',
            self::UNAUTHORIZED => 'Unauthorized access',
            self::FORBIDDEN => 'Access forbidden',
            self::CREATE_FAILED => 'Failed to create resource',
            self::UPDATE_FAILED => 'Failed to update resource',
            self::DELETE_FAILED => 'Failed to delete resource',
            self::INTERNAL_ERROR => 'Internal server error',
            self::ERROR => 'An error occurred',
        };
    }
}
