<?php

declare(strict_types=1);

namespace App\Modules\Core\Support;

use App\Modules\Core\Enums\ErrorCode;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/**
 * Helper class for standardizing API responses.
 *
 * Provides consistent JSON response format across the application.
 */
class ApiResponse
{
    /**
     * Return a successful JSON response.
     *
     * @param  array<string, mixed>|object|null  $data  Response data
     * @param  string  $message  Success message
     * @param  int  $statusCode  HTTP status code (default: 200)
     * @return JsonResponse JSON response with success structure
     */
    public static function success(
        array|object|null $data = null,
        string $message = 'Success',
        int $statusCode = 200
    ): JsonResponse {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Return an error JSON response.
     *
     * @param  string  $message  Error message
     * @param  ErrorCode|string  $errorCode  Error code identifier
     * @param  array<string, mixed>  $errors  Additional error details
     * @param  int  $statusCode  HTTP status code (default: 400)
     * @return JsonResponse JSON response with error structure
     */
    public static function error(
        string $message,
        ErrorCode|string $errorCode = ErrorCode::ERROR,
        array $errors = [],
        int $statusCode = 400
    ): JsonResponse {
        $code = $errorCode instanceof ErrorCode ? $errorCode->value : $errorCode;

        return response()->json([
            'status' => 'error',
            'error_code' => $code,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    /**
     * Return a paginated JSON response.
     *
     * @param  LengthAwarePaginator<int, mixed>  $paginator  Paginated results
     * @param  string  $message  Success message
     * @return JsonResponse JSON response with paginated data structure
     */
    public static function paginated(
        LengthAwarePaginator $paginator,
        string $message = 'Data retrieved successfully'
    ): JsonResponse {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Return a created JSON response (201).
     *
     * @param  array<string, mixed>|object|null  $data  Created resource data
     * @param  string  $message  Success message
     * @return JsonResponse JSON response with 201 status code
     */
    public static function created(array|object|null $data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    /**
     * Return a no content response (204).
     *
     * @return JsonResponse JSON response with 204 status code and empty body
     */
    public static function noContent(): JsonResponse
    {
        return response()->json([], 204);
    }
}
