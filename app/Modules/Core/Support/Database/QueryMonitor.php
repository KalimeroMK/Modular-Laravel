<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Database;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueryMonitor
{
    protected array $queries = [];
    protected float $totalTime = 0;
    protected int $queryCount = 0;
    protected bool $enabled = false;

    public function __construct()
    {
        $this->enabled = config('app.debug', false);
    }

    public function enable(): void
    {
        $this->enabled = true;
        $this->startMonitoring();
    }

    public function disable(): void
    {
        $this->enabled = false;
        $this->stopMonitoring();
    }

    public function startMonitoring(): void
    {
        if (!$this->enabled) {
            return;
        }

        DB::listen(function (QueryExecuted $query) {
            $this->queries[] = [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
                'connection' => $query->connectionName,
            ];

            $this->totalTime += $query->time;
            $this->queryCount++;
        });
    }

    public function stopMonitoring(): void
    {
        // DB::listen() is automatically removed when the listener is garbage collected
    }

    public function getQueries(): array
    {
        return $this->queries;
    }

    public function getTotalTime(): float
    {
        return $this->totalTime;
    }

    public function getQueryCount(): int
    {
        return $this->queryCount;
    }

    public function getAverageTime(): float
    {
        return $this->queryCount > 0 ? $this->totalTime / $this->queryCount : 0;
    }

    public function getSlowQueries(float $threshold = 100): array
    {
        return array_filter($this->queries, function ($query) use ($threshold) {
            return $query['time'] > $threshold;
        });
    }

    public function logSlowQueries(float $threshold = 100): void
    {
        $slowQueries = $this->getSlowQueries($threshold);
        
        if (!empty($slowQueries)) {
            Log::warning('Slow database queries detected', [
                'count' => count($slowQueries),
                'threshold' => $threshold,
                'queries' => $slowQueries,
            ]);
        }
    }

    public function reset(): void
    {
        $this->queries = [];
        $this->totalTime = 0;
        $this->queryCount = 0;
    }

    public function getReport(): array
    {
        return [
            'total_queries' => $this->queryCount,
            'total_time' => round($this->totalTime, 2),
            'average_time' => round($this->getAverageTime(), 2),
            'slow_queries' => count($this->getSlowQueries()),
            'queries' => $this->queries,
        ];
    }
}
