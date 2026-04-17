<?php

declare(strict_types=1);

namespace App\Modules\Core\Traits;

trait SwaggerTrait
{
    protected function getSuccessResponseStructure(): array
    {
        return [
            'status' => 'success',
            'message' => 'Operation completed successfully',
            'data' => [],
        ];
    }

    protected function getErrorResponseStructure(): array
    {
        return [
            'status' => 'error',
            'message' => 'An error occurred',
            'errors' => [],
        ];
    }

    protected function getValidationErrorResponseStructure(): array
    {
        return [
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => [],
        ];
    }

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
