<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Modules\Core\Support\YamlModule\YamlModuleParser;
use App\Modules\Core\Support\Generators\ModuleGenerator;
use Throwable;

class ModulesBuildFromYamlCommand extends Command
{
    protected $signature = 'modules:build-from-yaml {file=modules.yaml}';
    protected $description = 'Build multiple modules from a YAML definition file';

    public function handle(): void
    {
        $path = base_path($this->argument('file'));

        if (!file_exists($path)) {
            $this->error("YAML file not found at: $path");
            return;
        }

        $parser = new YamlModuleParser($path);
        $modules = $parser->parse();

        $allModules = collect($modules);
        $generator = app(ModuleGenerator::class);

        foreach ($modules as $name => $definition) {
            $this->info("Generating module: $name");

            try {
                $fields = array_map(function ($field) {
                    [$name, $type] = explode(':', $field);
                    return ['name' => trim($name), 'type' => trim($type)];
                }, $definition['fields']);

                $options = [
                    'relations'    => implode(',', $definition['relations']),
                    'exceptions'   => $definition['exceptions'] ?? false,
                    'observers'    => $definition['observers'] ?? false,
                    'policies'     => $definition['policies'] ?? false,
                    'repositories' => [],
                ];

                $generator->generate(Str::studly($name), $fields, $options);

                $this->info("✅ Module '{$name}' generated successfully.");
            } catch (Throwable $e) {
                $this->error("❌ Error generating module: " . $e->getMessage());
            }
        }

        $this->generatePivotMigrations($allModules);

        $this->info("✅ All modules processed.");
    }

    protected function generatePivotMigrations(\Illuminate\Support\Collection $modules): void
    {
        $names = $modules->keys();

        foreach ($names as $a) {
            foreach ($names as $b) {
                if ($a === $b) continue;

                $aRelations = $modules[$a]['raw_relations'] ?? [];
                $bRelations = $modules[$b]['raw_relations'] ?? [];

                if (
                    in_array($b, $aRelations['belongsToMany'] ?? []) &&
                    in_array($a, $bRelations['belongsToMany'] ?? [])
                ) {
                    $table = Str::snake(Str::pluralStudly(min($a, $b))) . '_' . Str::snake(Str::pluralStudly(max($a, $b)));
                    $first = Str::snake($a) . '_id';
                    $second = Str::snake($b) . '_id';

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

        if (!file_exists($stub)) {
            $this->warn("Pivot stub not found at: {$stub}");
            return;
        }

        $content = file_get_contents($stub);
        $content = str_replace(
            ['{{table}}', '{{first_column}}', '{{second_column}}'],
            [$table, $first, $second],
            $content
        );

        file_put_contents($target, $content);
        $this->info("📦 Pivot migration created: {$fileName}");
    }
}
