<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Support;

use App\Modules\Core\Support\ApiResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ApiResponseTest extends TestCase
{
    public function test_success_response_returns_correct_structure(): void
    {
        $data = ['id' => 1, 'name' => 'Test'];
        $response = ApiResponse::success($data, 'Success message');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Success message',
            'data' => $data,
        ]);
    }

    public function test_success_response_with_default_message(): void
    {
        $response = ApiResponse::success(['id' => 1]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Success',
            'data' => ['id' => 1],
        ]);
    }

    public function test_error_response_returns_correct_structure(): void
    {
        $errors = ['email' => ['The email field is required.']];
        $response = ApiResponse::error('Error message', 'ERROR_CODE', $errors, 400);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'error',
            'error_code' => 'ERROR_CODE',
            'message' => 'Error message',
            'errors' => $errors,
        ]);
    }

    public function test_error_response_with_default_values(): void
    {
        $response = ApiResponse::error('Error message');

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'error',
            'error_code' => 'ERROR',
            'message' => 'Error message',
            'errors' => [],
        ]);
    }

    public function test_created_response_returns_201_status(): void
    {
        $data = ['id' => 1, 'name' => 'Created Resource'];
        $response = ApiResponse::created($data, 'Resource created successfully');

        $response->assertStatus(201);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Resource created successfully',
            'data' => $data,
        ]);
    }

    public function test_created_response_with_default_message(): void
    {
        $response = ApiResponse::created(['id' => 1]);

        $response->assertStatus(201);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Resource created successfully',
        ]);
    }

    public function test_no_content_response_returns_204_status(): void
    {
        $response = ApiResponse::noContent();

        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEmpty($response->getContent());
    }

    public function test_paginated_response_returns_correct_structure(): void
    {
        $items = collect([
            ['id' => 1, 'name' => 'Item 1'],
            ['id' => 2, 'name' => 'Item 2'],
        ]);

        $paginator = new LengthAwarePaginator(
            $items,
            2, // total
            15, // per page
            1  // current page
        );

        $response = ApiResponse::paginated($paginator, 'Data retrieved successfully');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
                'from',
                'to',
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
        ]);

        $response->assertJson([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 15,
                'total' => 2,
            ],
        ]);
    }

    public function test_paginated_response_with_default_message(): void
    {
        $items = collect([['id' => 1]]);
        $paginator = new LengthAwarePaginator($items, 1, 15, 1);

        $response = ApiResponse::paginated($paginator);

        $response->assertJson([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
        ]);
    }
}
