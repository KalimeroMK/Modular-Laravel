<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Modules\Auth\Application\DTO\SessionTokenDTO;
use Tests\TestCase;

class SessionTokenDTOTest extends TestCase
{
    public function test_constructor_sets_properties(): void
    {

        $token = 'test-token';
        $type = 'Bearer';
        $expiresIn = 3600;

        $dto = new SessionTokenDTO($token, $type, $expiresIn);

        $this->assertEquals($token, $dto->token);
        $this->assertEquals($type, $dto->type);
        $this->assertEquals($expiresIn, $dto->expiresIn);
    }

    public function test_to_array_returns_correct_data(): void
    {

        $token = 'test-token';
        $type = 'Bearer';
        $expiresIn = 3600;
        $dto = new SessionTokenDTO($token, $type, $expiresIn);

        $result = $dto->toArray();

        $this->assertIsArray($result);
        $this->assertEquals($token, $result['token']);
        $this->assertEquals($type, $result['type']);
        $this->assertEquals($expiresIn, $result['expires_in']);
    }

    public function test_constructor_with_null_expires_in(): void
    {

        $token = 'test-token';
        $type = 'Bearer';

        $dto = new SessionTokenDTO($token, $type);

        $this->assertEquals($token, $dto->token);
        $this->assertEquals($type, $dto->type);
        $this->assertNull($dto->expiresIn);
    }
}
