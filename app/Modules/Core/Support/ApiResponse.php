<?php

declare(strict_types=1);

namespace App\Modules\Core\Support;

use App\Modules\Core\Enums\ErrorCode;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ApiResponse
{
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

    public static function paginated(
        LengthAwarePaginator $paginator,
        string $message = 'Data retrieved successfully',
        ?AnonymousResourceCollection $resourceCollection = null
    ): JsonResponse {
        $data = $resourceCollection?->response()->getData(true) ?? ['data' => $paginator->items()];

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data['data'] ?? [],
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

    public static function created(array|object|null $data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    public static function noContent(): JsonResponse
    {
        return response()->json([], 204);
    }
}
