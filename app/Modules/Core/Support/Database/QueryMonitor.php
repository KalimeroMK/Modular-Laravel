<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Database;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueryMonitor
{
    /**
     * @var array<int, array{sql: string, bindings: array<int, mixed>, time: float, connection: string}>
     */
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
        if (! $this->enabled) {
            return;
        }

        DB::listen(function (QueryExecuted $query): void {
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

    /**
     * @return array<int, array{sql: string, bindings: array<int, mixed>, time: float, connection: string}>
     */
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

    /**
     * @return array<int, array{sql: string, bindings: array<int, mixed>, time: float, connection: string}>
     */
    public function getSlowQueries(float $threshold = 100): array
    {
        return array_filter($this->queries, fn ($query) => $query['time'] > $threshold);
    }

    public function logSlowQueries(float $threshold = 100): void
    {
        $slowQueries = $this->getSlowQueries($threshold);

        if ($slowQueries !== []) {
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

    /**
     * @return array{total_queries: int, total_time: float, average_time: float, slow_queries: int, queries: array<int, array{sql: string, bindings: array<int, mixed>, time: float, connection: string}>}
     */
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
