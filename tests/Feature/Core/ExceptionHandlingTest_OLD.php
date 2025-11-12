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

        $response->assertStatus(404);
        $response->assertJson([
            'status' => 'error',
            'error_code' => 'RESOURCE_NOT_FOUND',
            'message' => 'Resource not found',
        ]);
    }

    public function test_model_not_found_exception_returns_404(): void
    {
        $response = $this->getJson('/api/v1/users/99999');

        $response->assertStatus(404);
        $response->assertJson([
            'status' => 'error',
            'error_code' => 'RESOURCE_NOT_FOUND',
            'message' => 'Resource not found',
        ]);
    }

    public function test_validation_exception_returns_422_with_errors(): void
    {
        $errors = ['email' => ['The email field is required.']];
        $exception = new ValidationException('Validation failed', $errors);
        $response = $exception->render();

        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
            'error_code' => 'VALIDATION_ERROR',
            'message' => 'Validation failed',
            'errors' => $errors,
        ]);
    }

    public function test_laravel_validation_exception_returns_422(): void
    {
        $response = $this->postJson('/api/v1/users', []);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'status',
            'error_code',
            'message',
            'errors',
        ]);
        $response->assertJson([
            'status' => 'error',
            'error_code' => 'VALIDATION_ERROR',
        ]);
    }

    public function test_create_exception_returns_500(): void
    {
        $exception = new CreateException('Failed to create resource');
        $response = $exception->render();

        $response->assertStatus(500);
        $response->assertJson([
            'status' => 'error',
            'error_code' => 'CREATE_FAILED',
            'message' => 'Failed to create resource',
        ]);
    }

    public function test_update_exception_returns_500(): void
    {
        $exception = new UpdateException('Failed to update resource');
        $response = $exception->render();

        $response->assertStatus(500);
        $response->assertJson([
            'status' => 'error',
            'error_code' => 'UPDATE_FAILED',
            'message' => 'Failed to update resource',
        ]);
    }

    public function test_delete_exception_returns_500(): void
    {
        $exception = new DeleteException('Failed to delete resource');
        $response = $exception->render();

        $response->assertStatus(500);
        $response->assertJson([
            'status' => 'error',
            'error_code' => 'DELETE_FAILED',
            'message' => 'Failed to delete resource',
        ]);
    }

    public function test_unauthorized_exception_returns_401(): void
    {
        $exception = new UnauthorizedException('Unauthorized access');
        $response = $exception->render();

        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'error',
            'error_code' => 'UNAUTHORIZED',
            'message' => 'Unauthorized access',
        ]);
    }

    public function test_forbidden_exception_returns_403(): void
    {
        $exception = new ForbiddenException('Access forbidden');
        $response = $exception->render();

        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'error',
            'error_code' => 'FORBIDDEN',
            'message' => 'Access forbidden',
        ]);
    }

    public function test_exception_response_includes_error_code(): void
    {
        $exception = new NotFoundException('Test message');
        $response = $exception->render();

        $response->assertJsonStructure([
            'status',
            'error_code',
            'message',
            'errors',
        ]);
    }
}
