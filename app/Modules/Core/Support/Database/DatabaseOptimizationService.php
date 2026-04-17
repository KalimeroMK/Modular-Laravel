<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Database;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DatabaseOptimizationService
{
    public function __construct(protected QueryMonitor $queryMonitor) {}

    public function optimizeQuery(Builder $query, array $indexes = []): Builder
    {
        if ($indexes !== []) {

            $index = $indexes[0];
            if ($index !== '') {
                $query->useIndex($index);
            }
        }

        return $query;
    }

    public function cacheQuery(string $key, callable $callback, int $ttl = 3600): mixed
    {
        return Cache::remember($key, $ttl, Closure::fromCallable($callback));
    }

    public function invalidateCachePattern(string $pattern): void
    {
        try {
            $store = Cache::getStore();
            if (method_exists($store, 'getRedis')) {
                $keys = $store->getRedis()->keys($pattern);
                if (! empty($keys)) {
                    $store->getRedis()->del($keys);
                }
            } else {

                Cache::forget($pattern);
            }
        } catch (Exception) {

        }
    }

    public function optimizePagination(Builder $query, int $perPage = 15, ?string $cursor = null): array
    {
        if ($cursor) {
            $query->where('id', '>', $cursor);
        }

        $results = $query->limit($perPage + 1)->get();
        $hasMore = $results->count() > $perPage;

        if ($hasMore) {
            $results->pop();
        }

        $lastModel = $results->last();
        $nextCursor = $hasMore && $lastModel !== null ? (int) ($lastModel->id ?? $lastModel->getKey() ?? 0) : null;

        return [
            'data' => $results,
            'next_cursor' => $nextCursor,
            'has_more' => $hasMore,
        ];
    }

    public function batchInsert(Model $model, array $data, int $chunkSize = 1000): bool
    {
        $validChunkSize = max(1, $chunkSize);
        $chunks = array_chunk($data, $validChunkSize);

        foreach ($chunks as $chunk) {
            $model->newQuery()->insert($chunk);
        }

        return true;
    }

    public function batchUpdate(Model $model, array $updates, string $key = 'id'): bool
    {
        $cases = [];
        $ids = [];
        $bindings = [];

        foreach ($updates as $update) {
            $id = $update[$key];
            $ids[] = $id;

            foreach ($update as $column => $value) {
                if ($column !== $key) {
                    $cases[$column][] = 'WHEN ? THEN ?';
                    $bindings[] = $id;
                    $bindings[] = $value;
                }
            }
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE {$model->getTable()} SET ";

        foreach ($cases as $column => $caseStatements) {
            $sql .= "{$column} = CASE {$key} ".implode(' ', $caseStatements).' END, ';
        }

        $sql = mb_rtrim($sql, ', ')." WHERE {$key} IN ({$placeholders})";
        $bindings = array_merge($bindings, $ids);

        return DB::update($sql, $bindings) > 0;
    }

    public function analyzeTable(string $table): array
    {
        if (! preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            throw new InvalidArgumentException('Invalid table name: '.$table);
        }

        try {
            $connection = DB::connection();
            $driver = $connection->getDriverName();

            if ($driver === 'sqlite') {

                return [
                    'table' => $table,
                    'status' => 'OK',
                    'rows' => DB::table($table)->count(),
                    'size' => ['Size_MB' => 0, 'Data_MB' => 0, 'Index_MB' => 0],
                ];
            }

            $result = DB::select("ANALYZE TABLE {$table}");

            return [
                'table' => $table,
                'status' => $result[0]->Msg_text ?? 'Unknown',
                'rows' => DB::table($table)->count(),
                'size' => $this->getTableSize($table),
            ];
        } catch (Exception $e) {
            return [
                'table' => $table,
                'status' => 'Error: '.$e->getMessage(),
                'rows' => 0,
                'size' => ['Size_MB' => 0, 'Data_MB' => 0, 'Index_MB' => 0],
            ];
        }
    }

    public function getTableSize(string $table): array
    {
        try {
            $result = DB::select("
                SELECT 
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size_MB',
                    ROUND((data_length / 1024 / 1024), 2) AS 'Data_MB',
                    ROUND((index_length / 1024 / 1024), 2) AS 'Index_MB'
                FROM information_schema.TABLES 
                WHERE table_schema = DATABASE() 
                AND table_name = ?
            ", [$table]);

            if (! empty($result) && isset($result[0])) {
                $row = $result[0];

                return [
                    'Size_MB' => (float) $row->Size_MB,
                    'Data_MB' => (float) $row->Data_MB,
                    'Index_MB' => (float) $row->Index_MB,
                ];
            }
        } catch (Exception) {

        }

        return ['Size_MB' => 0, 'Data_MB' => 0, 'Index_MB' => 0];
    }

    public function getSlowQueries(int $limit = 10): array
    {
        return DB::select('
            SELECT 
                sql_text,
                exec_count,
                avg_timer_wait/1000000000 as avg_time_seconds,
                max_timer_wait/1000000000 as max_time_seconds
            FROM performance_schema.events_statements_summary_by_digest 
            ORDER BY avg_timer_wait DESC 
            LIMIT ?
        ', [$limit]);
    }

    public function startMonitoring(): void
    {
        $this->queryMonitor->enable();
    }

    public function stopMonitoring(): array
    {
        $this->queryMonitor->disable();

        return $this->queryMonitor->getReport();
    }

    public function getConnectionInfo(): array
    {
        $connection = DB::connection();

        return [
            'driver' => $connection->getDriverName(),
            'database' => $connection->getDatabaseName(),
            'host' => $connection->getConfig('host'),
            'port' => $connection->getConfig('port'),
            'charset' => $connection->getConfig('charset'),
            'collation' => $connection->getConfig('collation'),
        ];
    }
}
