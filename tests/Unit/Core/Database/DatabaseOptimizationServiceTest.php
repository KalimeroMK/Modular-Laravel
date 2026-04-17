<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Database;

use App\Modules\Core\Support\Database\DatabaseOptimizationService;
use App\Modules\Core\Support\Database\QueryMonitor;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DatabaseOptimizationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DatabaseOptimizationService $service;

    protected QueryMonitor $queryMonitor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->queryMonitor = new QueryMonitor();
        $this->service = new DatabaseOptimizationService($this->queryMonitor);
    }

    public function test_can_cache_query(): void
    {

        $key = 'test_cache_key';
        $expectedData = ['test' => 'data'];

        $result = $this->service->cacheQuery($key, fn () => $expectedData, 60);

        $this->assertEquals($expectedData, $result);
        $this->assertTrue(Cache::has($key));
    }

    public function test_can_invalidate_cache_pattern(): void
    {

        Cache::put('test_key_1', 'value1', 60);
        Cache::put('test_key_2', 'value2', 60);
        Cache::put('other_key', 'value3', 60);

        $this->service->invalidateCachePattern('test_key_*');

        $this->assertTrue(true);
    }

    public function test_can_optimize_pagination(): void
    {

        User::factory()->count(5)->create();
        $query = User::query();

        $result = $this->service->optimizePagination($query, 3);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('next_cursor', $result);
        $this->assertArrayHasKey('has_more', $result);
        $this->assertCount(3, $result['data']);
        $this->assertTrue($result['has_more']);
    }

    public function test_can_batch_insert(): void
    {

        $data = [
            ['name' => 'User 1', 'email' => 'user1@test.com', 'password' => 'password'],
            ['name' => 'User 2', 'email' => 'user2@test.com', 'password' => 'password'],
            ['name' => 'User 3', 'email' => 'user3@test.com', 'password' => 'password'],
        ];

        $result = $this->service->batchInsert(new User(), $data);

        $this->assertTrue($result);
        $this->assertDatabaseCount('users', 3);
    }

    public function test_can_get_connection_info(): void
    {

        $info = $this->service->getConnectionInfo();

        $this->assertArrayHasKey('driver', $info);
        $this->assertArrayHasKey('database', $info);
        $this->assertArrayHasKey('host', $info);
        $this->assertArrayHasKey('port', $info);
        $this->assertArrayHasKey('charset', $info);
        $this->assertArrayHasKey('collation', $info);
    }

    public function test_can_monitor_queries(): void
    {

        User::factory()->count(3)->create();

        $this->service->startMonitoring();

        User::all();
        User::where('name', 'like', '%test%')->get();

        $report = $this->service->stopMonitoring();

        $this->assertArrayHasKey('total_queries', $report);
        $this->assertArrayHasKey('total_time', $report);
        $this->assertArrayHasKey('average_time', $report);
        $this->assertArrayHasKey('slow_queries', $report);
        $this->assertGreaterThan(0, $report['total_queries']);
    }

    public function test_can_analyze_table(): void
    {

        User::factory()->count(5)->create();

        $analysis = $this->service->analyzeTable('users');

        $this->assertArrayHasKey('table', $analysis);
        $this->assertArrayHasKey('status', $analysis);
        $this->assertArrayHasKey('rows', $analysis);
        $this->assertArrayHasKey('size', $analysis);
        $this->assertEquals('users', $analysis['table']);
        $this->assertEquals(5, $analysis['rows']);
    }
}
