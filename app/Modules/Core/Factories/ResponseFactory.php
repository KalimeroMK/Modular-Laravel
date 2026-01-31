<?php

declare(strict_types=1);

namespace App\Modules\Core\Factories;

use App\Modules\Core\Support\ApiResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;





class ResponseFactory
{
    




    public static function success(array|object|null $data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return ApiResponse::success($data, $message, $statusCode);
    }

    




    public static function error(string $message, string $errorCode = 'ERROR', array $errors = [], int $statusCode = 400): JsonResponse
    {
        return ApiResponse::error($message, $errorCode, $errors, $statusCode);
    }

    




    public static function paginated(LengthAwarePaginator $paginator, string $message = 'Data retrieved successfully'): JsonResponse
    {
        return ApiResponse::paginated($paginator, $message);
    }

    




    public static function created(array|object|null $data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return ApiResponse::created($data, $message);
    }

    


    public static function noContent(string $message = 'Operation completed successfully'): JsonResponse
    {
        return ApiResponse::noContent();
    }

    





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
