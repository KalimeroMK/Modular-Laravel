<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Modules\Core\Support\Database\DatabaseOptimizationService;
use Exception;
use Illuminate\Console\Command;

class DatabaseOptimizeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:optimize 
                            {--analyze : Analyze all tables}
                            {--slow-queries : Show slow queries}
                            {--connection-info : Show connection information}
                            {--monitor : Monitor query performance for 30 seconds}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize database performance and analyze queries';

    protected DatabaseOptimizationService $optimizationService;

    public function __construct(DatabaseOptimizationService $optimizationService)
    {
        parent::__construct();
        $this->optimizationService = $optimizationService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔧 Database Optimization Tool');
        $this->newLine();

        if ($this->option('analyze')) {
            $this->analyzeTables();
        }

        if ($this->option('slow-queries')) {
            $this->showSlowQueries();
        }

        if ($this->option('connection-info')) {
            $this->showConnectionInfo();
        }

        if ($this->option('monitor')) {
            $this->monitorQueries();
        }

        if (! $this->option('analyze') && ! $this->option('slow-queries') &&
            ! $this->option('connection-info') && ! $this->option('monitor')) {
            $this->showHelp();
        }

        return Command::SUCCESS;
    }

    protected function analyzeTables(): void
    {
        $this->info('📊 Analyzing Tables...');
        $this->newLine();

        $tables = ['users', 'roles', 'permissions', 'model_has_roles', 'model_has_permissions', 'role_has_permissions'];

        $headers = ['Table', 'Rows', 'Size (MB)', 'Data (MB)', 'Index (MB)', 'Status'];
        $rows = [];

        foreach ($tables as $table) {
            try {
                $analysis = $this->optimizationService->analyzeTable($table);
                $size = $this->optimizationService->getTableSize($table);

                $rows[] = [
                    $table,
                    number_format($analysis['rows']),
                    $size['Size_MB'],
                    $size['Data_MB'],
                    $size['Index_MB'],
                    $analysis['status'],
                ];
            } catch (Exception $e) {
                $rows[] = [$table, 'Error', '-', '-', '-', 'Failed'];
            }
        }

        $this->table($headers, $rows);
        $this->newLine();
    }

    protected function showSlowQueries(): void
    {
        $this->info('🐌 Slow Queries Analysis...');
        $this->newLine();

        try {
            $slowQueries = $this->optimizationService->getSlowQueries(10);

            if (empty($slowQueries)) {
                $this->warn('No slow queries found or performance_schema not available.');

                return;
            }

            $headers = ['Query', 'Exec Count', 'Avg Time (s)', 'Max Time (s)'];
            $rows = [];

            foreach ($slowQueries as $query) {
                $rows[] = [
                    mb_substr($query->sql_text, 0, 100).'...',
                    $query->exec_count,
                    round($query->avg_time_seconds, 4),
                    round($query->max_time_seconds, 4),
                ];
            }

            $this->table($headers, $rows);
        } catch (Exception $e) {
            $this->error('Error retrieving slow queries: '.$e->getMessage());
        }

        $this->newLine();
    }

    protected function showConnectionInfo(): void
    {
        $this->info('🔗 Database Connection Information...');
        $this->newLine();

        $info = $this->optimizationService->getConnectionInfo();

        $this->table(['Property', 'Value'], [
            ['Driver', $info['driver']],
            ['Database', $info['database']],
            ['Host', $info['host']],
            ['Port', $info['port']],
            ['Charset', $info['charset']],
            ['Collation', $info['collation']],
        ]);

        $this->newLine();
    }

    protected function monitorQueries(): void
    {
        $this->info('👀 Monitoring queries for 30 seconds...');
        $this->newLine();

        $this->optimizationService->startMonitoring();

        $bar = $this->output->createProgressBar(30);
        $bar->start();

        for ($i = 0; $i < 30; $i++) {
            sleep(1);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $report = $this->optimizationService->stopMonitoring();

        $this->info('📈 Query Performance Report:');
        $this->table(['Metric', 'Value'], [
            ['Total Queries', $report['total_queries']],
            ['Total Time (ms)', $report['total_time']],
            ['Average Time (ms)', $report['average_time']],
            ['Slow Queries', $report['slow_queries']],
        ]);

        if ($report['slow_queries'] > 0) {
            $this->warn("⚠️  Found {$report['slow_queries']} slow queries!");
        }

        $this->newLine();
    }

    protected function showHelp(): void
    {
        $this->info('Available options:');
        $this->line('  --analyze         Analyze all tables');
        $this->line('  --slow-queries    Show slow queries');
        $this->line('  --connection-info Show connection information');
        $this->line('  --monitor         Monitor query performance for 30 seconds');
        $this->newLine();
        $this->line('Example: php artisan db:optimize --analyze --slow-queries');
    }
}
