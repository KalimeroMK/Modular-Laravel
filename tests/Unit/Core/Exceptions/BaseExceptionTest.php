<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Exceptions;

use App\Modules\Core\Exceptions\CreateException;
use App\Modules\Core\Exceptions\DeleteException;
use App\Modules\Core\Exceptions\ForbiddenException;
use App\Modules\Core\Exceptions\NotFoundException;
use App\Modules\Core\Exceptions\UnauthorizedException;
use App\Modules\Core\Exceptions\UpdateException;
use App\Modules\Core\Exceptions\ValidationException;
use Tests\TestCase;

class BaseExceptionTest extends TestCase
{
    public function test_not_found_exception_has_correct_status_code(): void
    {
        $exception = new NotFoundException('Resource not found');

        $this->assertEquals(404, $exception->getStatusCode());
        $this->assertEquals('RESOURCE_NOT_FOUND', $exception->getErrorCode());
        $this->assertEquals('Resource not found', $exception->getMessage());
    }

    public function test_validation_exception_has_correct_status_code(): void
    {
        $errors = ['email' => ['The email field is required.']];
        $exception = new ValidationException('Validation failed', $errors);

        $this->assertEquals(422, $exception->getStatusCode());
        $this->assertEquals('VALIDATION_ERROR', $exception->getErrorCode());
        $this->assertEquals('Validation failed', $exception->getMessage());
    }

    public function test_create_exception_has_correct_status_code(): void
    {
        $exception = new CreateException('Failed to create resource');

        $this->assertEquals(500, $exception->getStatusCode());
        $this->assertEquals('CREATE_FAILED', $exception->getErrorCode());
        $this->assertEquals('Failed to create resource', $exception->getMessage());
    }

    public function test_update_exception_has_correct_status_code(): void
    {
        $exception = new UpdateException('Failed to update resource');

        $this->assertEquals(500, $exception->getStatusCode());
        $this->assertEquals('UPDATE_FAILED', $exception->getErrorCode());
        $this->assertEquals('Failed to update resource', $exception->getMessage());
    }

    public function test_delete_exception_has_correct_status_code(): void
    {
        $exception = new DeleteException('Failed to delete resource');

        $this->assertEquals(500, $exception->getStatusCode());
        $this->assertEquals('DELETE_FAILED', $exception->getErrorCode());
        $this->assertEquals('Failed to delete resource', $exception->getMessage());
    }

    public function test_unauthorized_exception_has_correct_status_code(): void
    {
        $exception = new UnauthorizedException('Unauthorized access');

        $this->assertEquals(401, $exception->getStatusCode());
        $this->assertEquals('UNAUTHORIZED', $exception->getErrorCode());
        $this->assertEquals('Unauthorized access', $exception->getMessage());
    }

    public function test_forbidden_exception_has_correct_status_code(): void
    {
        $exception = new ForbiddenException('Access forbidden');

        $this->assertEquals(403, $exception->getStatusCode());
        $this->assertEquals('FORBIDDEN', $exception->getErrorCode());
        $this->assertEquals('Access forbidden', $exception->getMessage());
    }

    public function test_exception_render_returns_json_response(): void
    {
        $exception = new NotFoundException('Resource not found');
        $response = $exception->render();

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('error', $data['status']);
        $this->assertEquals('RESOURCE_NOT_FOUND', $data['error_code']);
        $this->assertEquals('Resource not found', $data['message']);
    }

    public function test_validation_exception_includes_errors_in_response(): void
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
}
