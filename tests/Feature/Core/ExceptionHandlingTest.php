<?php

declare(strict_types=1);

namespace Tests\Feature\Core;

use App\Modules\Core\Exceptions\CreateException;
use App\Modules\Core\Exceptions\DeleteException;
use App\Modules\Core\Exceptions\ForbiddenException;
use App\Modules\Core\Exceptions\NotFoundException;
use App\Modules\Core\Exceptions\UnauthorizedException;
use App\Modules\Core\Exceptions\UpdateException;
use App\Modules\Core\Exceptions\ValidationException;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExceptionHandlingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_not_found_exception_returns_404_with_correct_structure(): void
    {
        $exception = new NotFoundException('Resource not found');
        $response = $exception->render();

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('error', $data['status']);
        $this->assertEquals('RESOURCE_NOT_FOUND', $data['error_code']);
        $this->assertEquals('Resource not found', $data['message']);
    }

    public function test_model_not_found_exception_returns_404(): void
    {
        // findOrFail() should throw ModelNotFoundException for non-existent IDs
        // This is handled by the exception handler in bootstrap/app.php
        $response = $this->getJson('/api/v1/users/99999');

        // findOrFail() should return 404
        $response->assertStatus(404);
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('error', $data['status']);
        $this->assertEquals('RESOURCE_NOT_FOUND', $data['error_code']);
        $this->assertEquals('Resource not found', $data['message']);
    }

    public function test_validation_exception_returns_422_with_errors(): void
    {
        $errors = ['email' => ['The email field is required.']];
        $exception = new ValidationException('Validation failed', $errors);
        $response = $exception->render();

        $this->assertEquals(422, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('error', $data['status']);
        $this->assertEquals('VALIDATION_ERROR', $data['error_code']);
        $this->assertEquals('Validation failed', $data['message']);
        $this->assertEquals($errors, $data['errors']);
    }

    public function test_laravel_validation_exception_returns_422(): void
    {
        $response = $this->postJson('/api/v1/users', []);

        $this->assertEquals(422, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('error_code', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('errors', $data);
        $this->assertEquals('error', $data['status']);
        $this->assertEquals('VALIDATION_ERROR', $data['error_code']);
    }

    public function test_create_exception_returns_500(): void
    {
        $exception = new CreateException('Failed to create resource');
        $response = $exception->render();

        $this->assertEquals(500, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('error', $data['status']);
        $this->assertEquals('CREATE_FAILED', $data['error_code']);
        $this->assertEquals('Failed to create resource', $data['message']);
    }

    public function test_update_exception_returns_500(): void
    {
        $exception = new UpdateException('Failed to update resource');
        $response = $exception->render();

        $this->assertEquals(500, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('error', $data['status']);
        $this->assertEquals('UPDATE_FAILED', $data['error_code']);
        $this->assertEquals('Failed to update resource', $data['message']);
    }

    public function test_delete_exception_returns_500(): void
    {
        $exception = new DeleteException('Failed to delete resource');
        $response = $exception->render();

        $this->assertEquals(500, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('error', $data['status']);
        $this->assertEquals('DELETE_FAILED', $data['error_code']);
        $this->assertEquals('Failed to delete resource', $data['message']);
    }

    public function test_unauthorized_exception_returns_401(): void
    {
        $exception = new UnauthorizedException('Unauthorized access');
        $response = $exception->render();

        $this->assertEquals(401, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('error', $data['status']);
        $this->assertEquals('UNAUTHORIZED', $data['error_code']);
        $this->assertEquals('Unauthorized access', $data['message']);
    }

    public function test_forbidden_exception_returns_403(): void
    {
        $exception = new ForbiddenException('Access forbidden');
        $response = $exception->render();

        $this->assertEquals(403, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('error', $data['status']);
        $this->assertEquals('FORBIDDEN', $data['error_code']);
        $this->assertEquals('Access forbidden', $data['message']);
    }

    public function test_exception_response_includes_error_code(): void
    {
        $exception = new NotFoundException('Test message');
        $response = $exception->render();

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('error_code', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('errors', $data);
    }
}
