<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Modules\Auth\Application\DTO\SessionTokenDTO;
use Tests\TestCase;

class SessionTokenDTOTest extends TestCase
{
    public function test_constructor_sets_properties(): void
    {
        // Arrange
        $token = 'test-token';
        $type = 'Bearer';
        $expiresIn = 3600;

        // Act
        $dto = new SessionTokenDTO($token, $type, $expiresIn);

        // Assert
        $this->assertEquals($token, $dto->token);
        $this->assertEquals($type, $dto->type);
        $this->assertEquals($expiresIn, $dto->expiresIn);
    }

    public function test_to_array_returns_correct_data(): void
    {
        // Arrange
        $token = 'test-token';
        $type = 'Bearer';
        $expiresIn = 3600;
        $dto = new SessionTokenDTO($token, $type, $expiresIn);

        // Act
        $result = $dto->toArray();

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals($token, $result['token']);
        $this->assertEquals($type, $result['type']);
        $this->assertEquals($expiresIn, $result['expires_in']);
    }

    public function test_constructor_with_null_expires_in(): void
    {
        // Arrange
        $token = 'test-token';
        $type = 'Bearer';

        // Act
        $dto = new SessionTokenDTO($token, $type, null);

        // Assert
        $this->assertEquals($token, $dto->token);
        $this->assertEquals($type, $dto->type);
        $this->assertNull($dto->expiresIn);
    }
}
