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

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals('Success message', $responseData['message']);
        $this->assertEquals($data, $responseData['data']);
    }

    public function test_success_response_with_default_message(): void
    {
        $response = ApiResponse::success(['id' => 1]);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals('Success', $responseData['message']);
        $this->assertEquals(['id' => 1], $responseData['data']);
    }

    public function test_error_response_returns_correct_structure(): void
    {
        $errors = ['email' => ['The email field is required.']];
        $response = ApiResponse::error('Error message', 'ERROR_CODE', $errors, 400);

        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('error', $responseData['status']);
        $this->assertEquals('ERROR_CODE', $responseData['error_code']);
        $this->assertEquals('Error message', $responseData['message']);
        $this->assertEquals($errors, $responseData['errors']);
    }

    public function test_error_response_with_default_values(): void
    {
        $response = ApiResponse::error('Error message');

        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('error', $responseData['status']);
        $this->assertEquals('ERROR', $responseData['error_code']);
        $this->assertEquals('Error message', $responseData['message']);
        $this->assertEquals([], $responseData['errors']);
    }

    public function test_created_response_returns_201_status(): void
    {
        $data = ['id' => 1, 'name' => 'Created Resource'];
        $response = ApiResponse::created($data, 'Resource created successfully');

        $this->assertEquals(201, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals('Resource created successfully', $responseData['message']);
        $this->assertEquals($data, $responseData['data']);
    }

    public function test_created_response_with_default_message(): void
    {
        $response = ApiResponse::created(['id' => 1]);

        $this->assertEquals(201, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals('Resource created successfully', $responseData['message']);
    }

    public function test_no_content_response_returns_204_status(): void
    {
        $response = ApiResponse::noContent();

        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals('[]', $response->getContent());
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

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals('Data retrieved successfully', $responseData['message']);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('meta', $responseData);
        $this->assertArrayHasKey('links', $responseData);
        $this->assertEquals(1, $responseData['meta']['current_page']);
        $this->assertEquals(1, $responseData['meta']['last_page']);
        $this->assertEquals(15, $responseData['meta']['per_page']);
        $this->assertEquals(2, $responseData['meta']['total']);
    }

    public function test_paginated_response_with_default_message(): void
    {
        $items = collect([['id' => 1]]);
        $paginator = new LengthAwarePaginator($items, 1, 15, 1);

        $response = ApiResponse::paginated($paginator);

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals('Data retrieved successfully', $responseData['message']);
    }
}
