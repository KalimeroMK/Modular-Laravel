<?php

declare(strict_types=1);

namespace Tests\Support;

use Illuminate\Testing\TestResponse;

trait TestHelpers
{
    protected function assertApiSuccess(TestResponse $response, int $statusCode = 200): void
    {
        $response->assertStatus($statusCode)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
            ])
            ->assertJson(['status' => 'success']);
    }

    protected function assertApiError(TestResponse $response, int $statusCode = 400): void
    {
        $response->assertStatus($statusCode)
            ->assertJsonStructure([
                'status',
                'error_code',
                'message',
            ])
            ->assertJson(['status' => 'error']);
    }

    protected function assertApiPaginated(TestResponse $response): void
    {
        $response->assertStatus(200)
            ->assertJsonStructure([
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
            ])
            ->assertJson(['status' => 'success']);
    }

    protected function assertApiCreated(TestResponse $response): void
    {
        $this->assertApiSuccess($response, 201);
    }

    protected function assertApiNoContent(TestResponse $response): void
    {
        $response->assertStatus(204);
    }
}
