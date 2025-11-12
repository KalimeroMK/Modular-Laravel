<?php

declare(strict_types=1);

namespace App\Modules\Core\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Base exception class for all custom exceptions.
 *
 * Provides consistent error response format with status codes and error codes.
 */
abstract class BaseException extends Exception
{
    /**
     * HTTP status code for this exception.
     */
    protected int $statusCode = 500;

    /**
     * Error code identifier.
     */
    protected string $errorCode = 'INTERNAL_ERROR';

    /**
     * Render the exception as an HTTP response.
     *
     * @return JsonResponse JSON response with error structure
     */
    final public function render(): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'error_code' => $this->errorCode,
            'message' => $this->getMessage(),
            'errors' => $this->getErrors(),
        ], $this->statusCode);
    }

    /**
     * Get HTTP status code for this exception
     */
    final public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get error code for this exception
     */
    final public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * Get additional error details
     *
     * @return array<string, mixed>
     */
    protected function getErrors(): array
    {
        return [];
    }
}
