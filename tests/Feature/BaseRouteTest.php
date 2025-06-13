<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class BaseRouteTest extends TestCase
{
    /**
     * Test that the base route returns a 200 or 302 (if redirected).
     * Ensures the app is running and responds to base route.
     */
    public function test_base_route_responds()
    {
        $response = $this->get('/');
        $status = $response->getStatusCode();
        $this->assertTrue(in_array($status, [200, 302]), "Base route should return 200 or 302, got {$status}");
    }

    /**
     * Test that the API is reachable and returns 404 for unknown endpoint (API is up).
     */
    public function test_api_404_for_unknown_route()
    {
        $response = $this->get('/api/this-route-should-not-exist');
        $response->assertStatus(404);
    }
}
