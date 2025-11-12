<?php

declare(strict_types=1);

namespace App\Modules\Core\Factories;

use App\Modules\Core\Support\ApiResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

/**
 * Factory for creating standardized API responses.
 * Follows Factory Pattern for complex object creation.
 */
class ResponseFactory
{
    /**
     * Create a success response.
     *
     * @param  array<string, mixed>|object|null  $data
     */
    public static function success(array|object|null $data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return ApiResponse::success($data, $message, $statusCode);
    }

    /**
     * Create an error response.
     *
     * @param  array<string, mixed>  $errors
     */
    public static function error(string $message, string $errorCode = 'ERROR', array $errors = [], int $statusCode = 400): JsonResponse
    {
        return ApiResponse::error($message, $errorCode, $errors, $statusCode);
    }

    /**
     * Create a paginated response.
     *
     * @param  LengthAwarePaginator<int, mixed>  $paginator
     */
    public static function paginated(LengthAwarePaginator $paginator, string $message = 'Data retrieved successfully'): JsonResponse
    {
        return ApiResponse::paginated($paginator, $message);
    }

    /**
     * Create a created response (201).
     *
     * @param  array<string, mixed>|object|null  $data
     */
    public static function created(array|object|null $data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return ApiResponse::created($data, $message);
    }

    /**
     * Create a no content response (204).
     */
    public static function noContent(string $message = 'Operation completed successfully'): JsonResponse
    {
        return ApiResponse::noContent();
    }

    /**
     * Create a response based on type string (for dynamic creation).
     *
     * @param  string  $type  Response type: success, error, paginated, created, no_content
     * @param  mixed  ...$args  Arguments for the response type
     */
    public static function create(string $type, ...$args): JsonResponse
    {
        return match ($type) {
            'success' => self::success(...$args),
            'error' => self::error(...$args),
            'paginated' => self::paginated(...$args),
            'created' => self::created(...$args),
            'no_content' => self::noContent(),
            default => throw new InvalidArgumentException("Unknown response type: {$type}"),
        };
    }
}
