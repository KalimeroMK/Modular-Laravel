<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Modules\Core\Support\Generators\ModuleGenerationTracker;
use App\Modules\Core\Support\Generators\ModuleGenerator;
use App\Modules\Core\Support\YamlModule\YamlModuleParser;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Throwable;

class ModulesBuildFromYamlCommand extends Command
{
    protected $signature = 'modules:build-from-yaml {file=modules.yaml}';

    protected $description = 'Build multiple modules from a YAML definition file';

    protected ?ModuleGenerationTracker $tracker = null;

    public function handle(): void
    {
        $path = base_path($this->argument('file'));

        if (! file_exists($path)) {
            $this->error("YAML file not found at: $path");

            return;
        }

        $parser = new YamlModuleParser($path);
        $modules = $parser->parse();

        $allModules = collect($modules);
        $generator = app(ModuleGenerator::class);

        $successCount = 0;
        $errorCount = 0;
        $failedModules = [];

        foreach ($modules as $name => $definition) {
            $this->info("Generating module: $name");

            try {
                // Backup RepositoryServiceProvider before modification (only once)
                static $providerBackedUp = false;
                if (! $providerBackedUp) {
                    $tracker = $this->getTracker();
                    $providerPath = app_path('Providers/RepositoryServiceProvider.php');
                    if (file_exists($providerPath) && ! isset($tracker->getModifiedFiles()[$providerPath])) {
                        $originalContent = file_get_contents($providerPath);
                        if ($originalContent !== false) {
                            $tracker->trackModifiedFile($providerPath, $originalContent);
                            $providerBackedUp = true;
                        }
                    }
                }

                $fields = array_map(function ($field) {
                    [$name, $type] = explode(':', $field);

                    return ['name' => mb_trim($name), 'type' => mb_trim($type)];
                }, $definition['fields']);

                /** @var array{relations: string, exceptions: mixed, observers: mixed, policies: mixed, events: mixed, enum: mixed, notifications: mixed, repositories: array{}, table?: string, relationships?: string, tracker?: mixed} $options */
                $options = [
                    'relations' => implode(',', $definition['relations']),
                    'exceptions' => $definition['exceptions'] ?? false,
                    'observers' => $definition['observers'] ?? false,
                    'policies' => $definition['policies'] ?? false,
                    'events' => $definition['events'] ?? false,
                    'enum' => $definition['enum'] ?? false,
                    'notifications' => $definition['notifications'] ?? false,
                    'repositories' => [],
                    'tracker' => $this->getTracker(),
                ];

                $options['table'] = Str::plural(Str::snake($name));
                $options['relationships'] = $this->buildRelationships($options['relations']);

                $generator->generate(Str::studly($name), $fields, $options);

                $successCount++;
                $this->info("✅ Module '{$name}' generated successfully.");
            } catch (Throwable $e) {
                $errorCount++;
                $failedModules[] = $name;
                $this->error('❌ Error generating module: '.$e->getMessage());

                // Rollback failed module
                $this->warn("Rolling back module '{$name}'...");
                $this->getTracker()->rollbackModule(Str::studly($name));
            }
        }

        $this->generatePivotMigrations($allModules);

        // Display statistics
        $stats = $this->getTracker()->getStatistics();
        $this->newLine();
        $this->info('📊 Generation Statistics:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Modules Generated', $stats['modules']],
                ['Total Files', $stats['files']],
                ['Successful', $successCount],
                ['Failed', $errorCount],
            ]
        );

        if (! empty($stats['files_by_module'])) {
            $this->newLine();
            $this->info('📁 Files by Module:');
            $filesTable = [['Module', 'Files']];
            foreach ($stats['files_by_module'] as $module => $count) {
                $filesTable[] = [$module, $count];
            }
            $this->table(['Module', 'Files'], array_slice($filesTable, 1));
        }

        if ($errorCount > 0) {
            $this->newLine();
            $this->warn("⚠️  {$errorCount} module(s) failed: ".implode(', ', $failedModules));
            $this->info('Rollback completed for failed modules.');
            // Restore modified files if all modules failed
            if ($successCount === 0) {
                $this->getTracker()->restoreModifiedFiles();
            }
        } else {
            $this->info('✅ All modules processed successfully.');
        }
    }

    protected function getTracker(): ModuleGenerationTracker
    {
        if (! $this->tracker instanceof ModuleGenerationTracker) {
            $this->tracker = app(ModuleGenerationTracker::class);
        }

        return $this->tracker;
    }

    /**
     * Generate pivot migrations for many-to-many relationships
     *
     * @param  \Illuminate\Support\Collection<string, array<string, mixed>>  $modules
     *
     * @phpstan-param \Illuminate\Support\Collection<string, array<string, mixed>> $modules
     */
    protected function generatePivotMigrations(\Illuminate\Support\Collection $modules): void
    {
        $names = $modules->keys();

        foreach ($names as $a) {
            foreach ($names as $b) {
                if ($a === $b) {
                    continue;
                }

                $aRelations = $modules[$a]['raw_relations'] ?? [];
                $bRelations = $modules[$b]['raw_relations'] ?? [];

                if (
                    in_array($b, $aRelations['belongsToMany'] ?? []) &&
                    in_array($a, $bRelations['belongsToMany'] ?? [])
                ) {
                    $table = Str::snake(Str::pluralStudly(min($a, $b))).'_'.Str::snake(Str::pluralStudly(max($a, $b)));
                    $first = Str::snake($a).'_id';
                    $second = Str::snake($b).'_id';

                    $this->generatePivotMigration($table, $first, $second);
                }
            }
        }
    }

    protected function generatePivotMigration(string $table, string $first, string $second): void
    {
        $timestamp = now()->format('Y_m_d_His');
        $fileName = "{$timestamp}_create_{$table}_pivot_table.php";
        $stub = base_path('stubs/module/Migration.pivot.stub');
        $target = base_path("database/migrations/{$fileName}");

        if (! file_exists($stub)) {
            $this->warn("Pivot stub not found at: {$stub}");

            return;
        }

        $fileContent = file_get_contents($stub);
        if ($fileContent === false) {
            $this->error("Could not read pivot stub file: {$stub}");

            return;
        }

        /** @var string $content */
        $content = $fileContent;
        $content = str_replace(
            ['{{table}}', '{{first_column}}', '{{second_column}}'],
            [$table, $first, $second],
            $content
        );

        file_put_contents($target, $content);
        $this->info("📦 Pivot migration created: {$fileName}");
    }

    /**
     * Build relationship methods from relation definitions
     *
     * @param  string|array<int, string>  $relations
     */
    protected function buildRelationships(string|array $relations): string
    {
        if (empty($relations)) {
            return '';
        }

        if (is_string($relations)) {
            $relations = explode(',', $relations);
        }

        $lines = [];
        $imports = [];
        foreach ($relations as $rel) {
            $parts = explode(':', $rel);
            if (count($parts) < 2) {
                continue;
            }
            $relName = mb_trim($parts[0]);
            $relType = mb_trim($parts[1]);
            $relModel = $parts[2] ?? ucfirst($relName);

            // Handle polymorphic relationships
            if (in_array($relType, ['morphTo', 'morphMany', 'morphOne', 'morphToMany'])) {
                $morphName = $parts[3] ?? $relName;
                $lines[] = $this->buildPolymorphicRelationship($relName, $relType, $relModel, $parts);
            } else {
                // Add import for the related model
                $imports[] = "use App\\Modules\\{$relModel}\\Infrastructure\\Models\\{$relModel};";
                $lines[] = "    public function {$relName}()\n    {\n        return \$this->{$relType}({$relModel}::class);\n    }";
            }
        }

        // Remove duplicate imports
        $imports = array_unique($imports);

        return implode("\n", $imports)."\n\n".implode("\n", $lines);
    }

    /**
     * Build polymorphic relationship method
     *
     * @param  array<int, string>  $parts
     */
    protected function buildPolymorphicRelationship(string $relName, string $relType, string $relModel, array $parts): string
    {
        switch ($relType) {
            case 'morphTo':
                return "    public function {$relName}()\n    {\n        return \$this->morphTo();\n    }";

            case 'morphMany':
                $morphName = $parts[3] ?? $relName;

                return "    public function {$relName}()\n    {\n        return \$this->morphMany({$relModel}::class, '{$morphName}');\n    }";

            case 'morphOne':
                $morphName = $parts[3] ?? $relName;

                return "    public function {$relName}()\n    {\n        return \$this->morphOne({$relModel}::class, '{$morphName}');\n    }";

            case 'morphToMany':
                $morphName = $parts[3] ?? $relName;

                return "    public function {$relName}()\n    {\n        return \$this->morphToMany({$relModel}::class, '{$morphName}');\n    }";

            default:
                // Fallback to standard relationship
                return "    public function {$relName}()\n    {\n        return \$this->{$relType}({$relModel}::class);\n    }";
        }
    }
}
