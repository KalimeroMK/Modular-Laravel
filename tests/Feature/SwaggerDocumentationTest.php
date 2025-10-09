<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SwaggerDocumentationTest extends TestCase
{
    use RefreshDatabase;

    public function test_swagger_documentation_is_generated(): void
    {
        // Run the swagger generation command
        $this->artisan('l5-swagger:generate')->assertExitCode(0);

        // Check if swagger documentation files exist
        $this->assertTrue(File::exists(storage_path('api-docs/api-docs.json')));
    }

    public function test_swagger_json_file_contains_valid_structure(): void
    {
        $this->artisan('l5-swagger:generate');

        $jsonContent = File::get(storage_path('api-docs/api-docs.json'));
        $json = json_decode($jsonContent, true);

        // Verify basic Swagger structure
        $this->assertArrayHasKey('openapi', $json);
        $this->assertArrayHasKey('info', $json);
        $this->assertArrayHasKey('paths', $json);
        $this->assertArrayHasKey('components', $json);
    }

    public function test_auth_module_swagger_documentation_exists(): void
    {
        $this->artisan('l5-swagger:generate');

        $jsonContent = File::get(storage_path('api-docs/api-docs.json'));
        $json = json_decode($jsonContent, true);

        // Check if auth endpoints are documented
        $this->assertArrayHasKey('/api/v1/auth/register', $json['paths']);
        $this->assertArrayHasKey('/api/v1/auth/login', $json['paths']);
        $this->assertArrayHasKey('/api/v1/auth/logout', $json['paths']);
        $this->assertArrayHasKey('/api/v1/auth/me', $json['paths']);
        $this->assertArrayHasKey('/api/v1/auth/forgot-password', $json['paths']);
        $this->assertArrayHasKey('/api/v1/reset-password', $json['paths']);

        // Verify the Authentication tag exists
        $tags = collect($json['tags'])->pluck('name')->toArray();
        $this->assertContains('Authentication', $tags);
    }

    public function test_user_module_swagger_documentation_exists(): void
    {
        $this->artisan('l5-swagger:generate');

        $jsonContent = File::get(storage_path('api-docs/api-docs.json'));
        $json = json_decode($jsonContent, true);

        // Check if user endpoints are documented
        $this->assertArrayHasKey('/api/v1/users', $json['paths']);
        $this->assertArrayHasKey('/api/v1/users/{id}', $json['paths']);
    }

    public function test_role_module_swagger_documentation_exists(): void
    {
        $this->artisan('l5-swagger:generate');

        $jsonContent = File::get(storage_path('api-docs/api-docs.json'));
        $json = json_decode($jsonContent, true);

        // Check if role endpoints are documented
        $this->assertArrayHasKey('/api/v1/roles', $json['paths']);
        $this->assertArrayHasKey('/api/v1/roles/{id}', $json['paths']);
    }

    public function test_permission_module_swagger_documentation_exists(): void
    {
        $this->artisan('l5-swagger:generate');

        $jsonContent = File::get(storage_path('api-docs/api-docs.json'));
        $json = json_decode($jsonContent, true);

        // Check if permission endpoints are documented
        $this->assertArrayHasKey('/api/v1/permissions', $json['paths']);
        $this->assertArrayHasKey('/api/v1/permissions/{id}', $json['paths']);
    }

    public function test_swagger_documentation_has_correct_info(): void
    {
        $this->artisan('l5-swagger:generate');

        $jsonContent = File::get(storage_path('api-docs/api-docs.json'));
        $json = json_decode($jsonContent, true);

        $this->assertEquals('3.0.0', $json['openapi']);
        $this->assertEquals('Modular Laravel API', $json['info']['title']);
        $this->assertEquals('API documentation for Modular Laravel application', $json['info']['description']);
        $this->assertEquals('1.0.0', $json['info']['version']);
    }

    public function test_swagger_documentation_has_security_schemes(): void
    {
        $this->artisan('l5-swagger:generate');

        $jsonContent = File::get(storage_path('api-docs/api-docs.json'));
        $json = json_decode($jsonContent, true);

        $this->assertArrayHasKey('securitySchemes', $json['components']);
        $this->assertArrayHasKey('bearerAuth', $json['components']['securitySchemes']);
        $this->assertEquals('http', $json['components']['securitySchemes']['bearerAuth']['type']);
        $this->assertEquals('bearer', $json['components']['securitySchemes']['bearerAuth']['scheme']);
    }

    public function test_swagger_documentation_has_response_schemas(): void
    {
        $this->artisan('l5-swagger:generate');

        $jsonContent = File::get(storage_path('api-docs/api-docs.json'));
        $json = json_decode($jsonContent, true);

        $this->assertArrayHasKey('schemas', $json['components']);

        // Check for common response schemas
        $this->assertArrayHasKey('SuccessResponse', $json['components']['schemas']);
        $this->assertArrayHasKey('ErrorResponse', $json['components']['schemas']);
        $this->assertArrayHasKey('ValidationErrorResponse', $json['components']['schemas']);
    }
}
