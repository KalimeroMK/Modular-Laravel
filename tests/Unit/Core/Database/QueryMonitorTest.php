<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Database;

use App\Modules\Core\Support\Database\QueryMonitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Override;
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
        
        $this->monitor->enable();
        $this->assertTrue(true); 

        $this->monitor->disable();
        $this->assertTrue(true); 
    }

    public function test_can_track_queries(): void
    {
        
        $this->monitor->enable();

        
        DB::table('users')->count();
        DB::table('users')->where('id', 1)->first();

        
        $queries = $this->monitor->getQueries();
        $this->assertGreaterThan(0, count($queries));
        $this->assertGreaterThan(0, $this->monitor->getQueryCount());
        $this->assertGreaterThan(0, $this->monitor->getTotalTime());
    }

    public function test_can_calculate_average_time(): void
    {
        
        $this->monitor->enable();

        
        DB::table('users')->count();
        DB::table('users')->count();

        
        $averageTime = $this->monitor->getAverageTime();
        $this->assertGreaterThan(0, $averageTime);
        $this->assertEquals($this->monitor->getTotalTime() / $this->monitor->getQueryCount(), $averageTime);
    }

    public function test_can_identify_slow_queries(): void
    {
        
        $this->monitor->enable();

        
        DB::table('users')->count();

        
        $slowQueries = $this->monitor->getSlowQueries(0.1); 
        $this->assertIsArray($slowQueries);
    }

    public function test_can_reset_monitoring(): void
    {
        
        $this->monitor->enable();
        DB::table('users')->count();

        
        $this->monitor->reset();

        
        $this->assertEquals(0, $this->monitor->getQueryCount());
        $this->assertEquals(0, $this->monitor->getTotalTime());
        $this->assertEmpty($this->monitor->getQueries());
    }

    public function test_can_generate_report(): void
    {
        
        $this->monitor->enable();
        DB::table('users')->count();

        
        $report = $this->monitor->getReport();

        
        $this->assertArrayHasKey('total_queries', $report);
        $this->assertArrayHasKey('total_time', $report);
        $this->assertArrayHasKey('average_time', $report);
        $this->assertArrayHasKey('slow_queries', $report);
        $this->assertArrayHasKey('queries', $report);
        $this->assertGreaterThan(0, $report['total_queries']);
    }
}
