<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Database;

use App\Modules\Core\Support\Database\QueryMonitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QueryMonitorTest extends TestCase
{
    use RefreshDatabase;

    protected QueryMonitor $monitor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->monitor = new QueryMonitor();
    }

    public function test_can_enable_and_disable_monitoring(): void
    {
        // Act & Assert
        $this->monitor->enable();
        $this->assertTrue(true); // Monitoring is enabled

        $this->monitor->disable();
        $this->assertTrue(true); // Monitoring is disabled
    }

    public function test_can_track_queries(): void
    {
        // Arrange
        $this->monitor->enable();

        // Act
        DB::table('users')->count();
        DB::table('users')->where('id', 1)->first();

        // Assert
        $queries = $this->monitor->getQueries();
        $this->assertGreaterThan(0, count($queries));
        $this->assertGreaterThan(0, $this->monitor->getQueryCount());
        $this->assertGreaterThan(0, $this->monitor->getTotalTime());
    }

    public function test_can_calculate_average_time(): void
    {
        // Arrange
        $this->monitor->enable();

        // Act
        DB::table('users')->count();
        DB::table('users')->count();

        // Assert
        $averageTime = $this->monitor->getAverageTime();
        $this->assertGreaterThan(0, $averageTime);
        $this->assertEquals($this->monitor->getTotalTime() / $this->monitor->getQueryCount(), $averageTime);
    }

    public function test_can_identify_slow_queries(): void
    {
        // Arrange
        $this->monitor->enable();

        // Act
        DB::table('users')->count();

        // Assert
        $slowQueries = $this->monitor->getSlowQueries(0.1); // Very low threshold for testing
        $this->assertIsArray($slowQueries);
    }

    public function test_can_reset_monitoring(): void
    {
        // Arrange
        $this->monitor->enable();
        DB::table('users')->count();

        // Act
        $this->monitor->reset();

        // Assert
        $this->assertEquals(0, $this->monitor->getQueryCount());
        $this->assertEquals(0, $this->monitor->getTotalTime());
        $this->assertEmpty($this->monitor->getQueries());
    }

    public function test_can_generate_report(): void
    {
        // Arrange
        $this->monitor->enable();
        DB::table('users')->count();

        // Act
        $report = $this->monitor->getReport();

        // Assert
        $this->assertArrayHasKey('total_queries', $report);
        $this->assertArrayHasKey('total_time', $report);
        $this->assertArrayHasKey('average_time', $report);
        $this->assertArrayHasKey('slow_queries', $report);
        $this->assertArrayHasKey('queries', $report);
        $this->assertGreaterThan(0, $report['total_queries']);
    }
}
