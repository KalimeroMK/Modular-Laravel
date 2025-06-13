<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModuleSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_routes_exist()
    {
        $this->getJson('/api/v1/users')->assertStatus(200);
    }
}
