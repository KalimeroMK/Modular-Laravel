<?php

declare(strict_types=1);

namespace App\Modules\Core\Traits;

/**
 * @OA\Info(
 *     title="Modular Laravel API",
 *     version="1.0.0",
 *     description="API documentation for Modular Laravel application",
 *
 *     @OA\Contact(
 *         email="admin@example.com",
 *         name="API Support"
 *     ),
 *
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="Authentication endpoints"
 * )
 * @OA\Tag(
 *     name="Users",
 *     description="User management endpoints"
 * )
 * @OA\Tag(
 *     name="Roles",
 *     description="Role management endpoints"
 * )
 * @OA\Tag(
 *     name="Permissions",
 *     description="Permission management endpoints"
 * )
 *
 * @OA\Schema(
 *     schema="SuccessResponse",
 *
 *     @OA\Property(property="status", type="string", example="success"),
 *     @OA\Property(property="message", type="string", example="Operation completed successfully"),
 *     @OA\Property(property="data", type="object")
 * )
 *
 * @OA\Schema(
 *     schema="ErrorResponse",
 *
 *     @OA\Property(property="status", type="string", example="error"),
 *     @OA\Property(property="message", type="string", example="An error occurred"),
 *     @OA\Property(property="errors", type="object")
 * )
 *
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *
 *     @OA\Property(property="status", type="string", example="error"),
 *     @OA\Property(property="message", type="string", example="Validation failed"),
 *     @OA\Property(property="errors", type="object")
 * )
 *
 * @OA\Schema(
 *     schema="PaginationResponse",
 *
 *     @OA\Property(property="status", type="string", example="success"),
 *     @OA\Property(property="message", type="string", example="Data retrieved successfully"),
 *     @OA\Property(property="data", type="object",
 *         @OA\Property(property="data", type="array", @OA\Items(type="object")),
 *         @OA\Property(property="current_page", type="integer", example=1),
 *         @OA\Property(property="last_page", type="integer", example=10),
 *         @OA\Property(property="per_page", type="integer", example=15),
 *         @OA\Property(property="total", type="integer", example=150)
 *     )
 * )
 */
trait SwaggerTrait
{
    /**
     * Get common success response structure
     *
     * @return array<string, mixed>
     */
    protected function getSuccessResponseStructure(): array
    {
        return [
            'status' => 'success',
            'message' => 'Operation completed successfully',
            'data' => [],
        ];
    }

    /**
     * Get common error response structure
     *
     * @return array<string, mixed>
     */
    protected function getErrorResponseStructure(): array
    {
        return [
            'status' => 'error',
            'message' => 'An error occurred',
            'errors' => [],
        ];
    }

    /**
     * Get common validation error response structure
     *
     * @return array<string, mixed>
     */
    protected function getValidationErrorResponseStructure(): array
    {
        return [
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => [],
        ];
    }

    /**
     * Get common pagination response structure
     *
     * @return array<string, mixed>
     */
    protected function getPaginationResponseStructure(): array
    {
        return [
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => [
                'data' => [],
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 15,
                'total' => 0,
            ],
        ];
    }
}
