<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Database;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DatabaseOptimizationService
{
    protected QueryMonitor $queryMonitor;

    public function __construct(QueryMonitor $queryMonitor)
    {
        $this->queryMonitor = $queryMonitor;
    }

    /**
     * Optimize query with proper indexing hints
     */
    public function optimizeQuery(Builder $query, array $indexes = []): Builder
    {
        if (!empty($indexes)) {
            $query->useIndex($indexes);
        }

        return $query;
    }

    /**
     * Add query caching with automatic invalidation
     */
    public function cacheQuery(string $key, callable $callback, int $ttl = 3600): mixed
    {
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Invalidate cache patterns
     */
    public function invalidateCachePattern(string $pattern): void
    {
        try {
            $store = Cache::getStore();
            if (method_exists($store, 'getRedis')) {
                $keys = $store->getRedis()->keys($pattern);
                if (!empty($keys)) {
                    $store->getRedis()->del($keys);
                }
            } else {
                // Fallback for non-Redis stores
                Cache::forget($pattern);
            }
        } catch (\Exception $e) {
            // Silently fail for unsupported cache stores
        }
    }

    /**
     * Optimize pagination with cursor-based pagination for large datasets
     */
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

        $nextCursor = $hasMore ? $results->last()?->id : null;

        return [
            'data' => $results,
            'next_cursor' => $nextCursor,
            'has_more' => $hasMore,
        ];
    }

    /**
     * Batch operations for better performance
     */
    public function batchInsert(Model $model, array $data, int $chunkSize = 1000): bool
    {
        $chunks = array_chunk($data, $chunkSize);
        
        foreach ($chunks as $chunk) {
            $model->newQuery()->insert($chunk);
        }

        return true;
    }

    /**
     * Optimize bulk updates
     */
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
                    $cases[$column][] = "WHEN {$id} THEN ?";
                    $bindings[] = $value;
                }
            }
        }

        $idsString = implode(',', $ids);
        $sql = "UPDATE {$model->getTable()} SET ";
        
        foreach ($cases as $column => $caseStatements) {
            $sql .= "{$column} = CASE {$key} " . implode(' ', $caseStatements) . " END, ";
        }
        
        $sql = rtrim($sql, ', ') . " WHERE {$key} IN ({$idsString})";

        return DB::update($sql, $bindings) > 0;
    }

    /**
     * Analyze table performance
     */
    public function analyzeTable(string $table): array
    {
        try {
            $connection = DB::connection();
            $driver = $connection->getDriverName();
            
            if ($driver === 'sqlite') {
                // SQLite doesn't support ANALYZE TABLE, return basic info
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
        } catch (\Exception $e) {
            return [
                'table' => $table,
                'status' => 'Error: ' . $e->getMessage(),
                'rows' => 0,
                'size' => ['Size_MB' => 0, 'Data_MB' => 0, 'Index_MB' => 0],
            ];
        }
    }

    /**
     * Get table size information
     */
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

            if (!empty($result) && isset($result[0])) {
                $row = $result[0];
                return [
                    'Size_MB' => (float) $row->Size_MB,
                    'Data_MB' => (float) $row->Data_MB,
                    'Index_MB' => (float) $row->Index_MB,
                ];
            }
        } catch (\Exception $e) {
            // Fallback for databases that don't support information_schema queries
        }

        return ['Size_MB' => 0, 'Data_MB' => 0, 'Index_MB' => 0];
    }

    /**
     * Get slow query log
     */
    public function getSlowQueries(int $limit = 10): array
    {
        return DB::select("
            SELECT 
                sql_text,
                exec_count,
                avg_timer_wait/1000000000 as avg_time_seconds,
                max_timer_wait/1000000000 as max_time_seconds
            FROM performance_schema.events_statements_summary_by_digest 
            ORDER BY avg_timer_wait DESC 
            LIMIT ?
        ", [$limit]);
    }

    /**
     * Monitor query performance
     */
    public function startMonitoring(): void
    {
        $this->queryMonitor->enable();
    }

    /**
     * Stop monitoring and get report
     */
    public function stopMonitoring(): array
    {
        $this->queryMonitor->disable();
        return $this->queryMonitor->getReport();
    }

    /**
     * Get database connection info
     */
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
