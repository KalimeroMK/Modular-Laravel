<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class BaseRouteTest extends TestCase
{
    



    public function test_base_route_responds()
    {
        $this->markTestSkipped('Base route not implemented - API-only project');

        $response = $this->get('/');
        $status = $response->getStatusCode();
        $this->assertTrue(in_array($status, [200, 302]), "Base route should return 200 or 302, got {$status}");
    }

    


    public function test_api_404_for_unknown_route()
    {
        $response = $this->get('/api/this-route-should-not-exist');
        $response->assertStatus(404);
    }
}
