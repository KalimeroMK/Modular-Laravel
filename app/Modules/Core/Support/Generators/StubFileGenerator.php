<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class StubFileGenerator
{
    public function __construct(protected Filesystem $files) {}

    /**
     * @throws FileNotFoundException
     */
    public function generate(string $moduleName, array $fields, array $options): void
    {
        $basePath = app_path("Modules/{$moduleName}");
        $replacements = [
            '{{module}}' => $moduleName,
            '{{module_lower}}' => mb_strtolower($moduleName),
            '{{moduleVar}}' => mb_strtolower($moduleName),
            '{{table}}' => $options['table'] ?? Str::plural(Str::snake($moduleName)),
            '{{timestamp}}' => now()->format('Y_m_d_His'),
        ];

        $stubMap = [
            'Interfaces/{{module}}Interface.php' => 'stubs/module/Interface.stub',
            'Repositories/{{module}}Repository.php' => 'stubs/module/Repository.stub',
            'Models/{{module}}.php' => 'stubs/module/Model.stub',
            'Database/Factories/{{module}}Factory.php' => 'stubs/module/Factory.stub',
            'routes/api.php' => 'stubs/module/routes/api.stub',
            'Http/Controllers/{{module}}Controller.php' => 'stubs/module/Http/Controllers/Controller.stub',
            'Http/Resources/{{module}}Resource.php' => 'stubs/module/Http/Resource/Resource.stub',
            'Database/migrations/{{timestamp}}_create_{{table}}_table.php' => 'stubs/module/Migration.stub',
        ];

        foreach ($stubMap as $target => $stubPath) {
            $targetPath = $basePath.'/'.str_replace(array_keys($replacements), array_values($replacements), $target);
            $stubFullPath = base_path($stubPath);

            if (! $this->files->exists($stubFullPath)) {
                continue;
            }

            $currentReplacements = $replacements;

            if (Str::endsWith($stubPath, 'Factory.stub')) {
                $currentReplacements['{{factory_fields}}'] = $this->buildFactoryFields($fields);
            }

            if (Str::endsWith($stubPath, 'Migration.stub')) {
                $currentReplacements['{{migration_fields}}'] = $this->buildMigrationFields($fields);
            }

            if (Str::endsWith($stubPath, 'Resource.stub')) {
                $currentReplacements['{{resource_fields}}'] = $this->buildResourceFields($fields);
            }

            if (Str::endsWith($stubPath, 'Model.stub')) {
                $currentReplacements['{{table}}'] = $replacements['{{table}}'];
                $currentReplacements['{{fillable}}'] = implode(', ', array_map(fn ($f) => "'{$f['name']}'", $fields));
                $currentReplacements['{{casts}}'] = $this->buildCasts($fields);
                $currentReplacements['{{phpdoc_block}}'] = $this->buildPhpDoc($fields);
                $currentReplacements['{{relationships}}'] = $options['relationships'] ?? '';
            }

            $content = $this->files->get($stubFullPath);
            $content = str_replace(array_keys($currentReplacements), array_values($currentReplacements), $content);

            $this->files->ensureDirectoryExists(dirname($targetPath));
            $this->files->put($targetPath, $content);
        }
    }

    protected function buildFactoryFields(array $fields): string
    {
        $lines = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $field['type'];
            $faker = match ($type) {
                'string', 'char', 'text', 'mediumText', 'longText' => "'{$name}' => \$this->faker->sentence",
                'float', 'double', 'decimal' => "'{$name}' => \$this->faker->randomFloat(2, 0, 1000)",
                'int', 'integer', 'bigint', 'tinyInteger', 'smallInteger', 'mediumInteger', 'unsignedBigInteger' => "'{$name}' => \$this->faker->numberBetween(0, 1000)",
                'bool', 'boolean' => "'{$name}' => \$this->faker->boolean",
                'date', 'datetime', 'timestamp', 'time', 'year' => "'{$name}' => now()",
                'uuid' => "'{$name}' => (string) \Str::uuid()",
                'ipAddress' => "'{$name}' => \$this->faker->ipv4",
                'macAddress' => "'{$name}' => \$this->faker->macAddress",
                'array', 'json' => "'{$name}' => []",
                'enum' => "'{$name}' => 'option1'",
                default => "'{$name}' => null",
            };

            $lines[] = "            {$faker},";
        }

        return implode("\n", $lines);
    }

    protected function buildMigrationFields(array $fields): string
    {
        $lines = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $field['type'];

            if ($type === 'foreign') {
                $references = $field['references'] ?? 'id';
                $on = $field['on'] ?? 'users';
                $lines[] = "            \$table->foreignId('{$name}')->constrained('{$on}')->references('{$references}');";
            } else {
                $column = match ($type) {
                    'char' => "char('{$name}', 100)",
                    'text' => "text('{$name}')",
                    'mediumText' => "mediumText('{$name}')",
                    'longText' => "longText('{$name}')",
                    'tinyInteger' => "tinyInteger('{$name}')",
                    'smallInteger' => "smallInteger('{$name}')",
                    'mediumInteger' => "mediumInteger('{$name}')",
                    'bigInteger' => "bigInteger('{$name}')",
                    'unsignedBigInteger' => "unsignedBigInteger('{$name}')",
                    'increments' => "increments('{$name}')",
                    'bigIncrements' => "bigIncrements('{$name}')",
                    'double' => "double('{$name}', 15, 8)",
                    'decimal' => "decimal('{$name}', 8, 2)",
                    'date', 'datetime', 'timestamp', 'time', 'year' => "{$type}('{$name}')",
                    'boolean' => "boolean('{$name}')",
                    'enum' => "enum('{$name}', ['option1', 'option2'])",
                    'json' => "json('{$name}')",
                    'binary' => "binary('{$name}')",
                    'uuid' => "uuid('{$name}')",
                    'ipAddress' => "ipAddress('{$name}')",
                    'macAddress' => "macAddress('{$name}')",
                    default => "string('{$name}')",
                };

                $lines[] = "            \$table->{$column};";
            }
        }

        return implode("\n", $lines);
    }

    protected function buildResourceFields(array $fields): string
    {
        $lines = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $lines[] = "            '{$name}' => \$this->{$name},";
        }

        return implode("\n", $lines);
    }

    protected function buildCasts(array $fields): string
    {
        $lines = [];

        foreach ($fields as $field) {
            $type = match ($field['type']) {
                'float', 'double', 'decimal' => 'float',
                'int', 'integer', 'bigint', 'tinyInteger', 'smallInteger', 'mediumInteger', 'unsignedBigInteger' => 'int',
                'bool', 'boolean' => 'bool',
                'array', 'json' => 'array',
                default => null,
            };

            if ($type) {
                $lines[] = "        '{$field['name']}' => '{$type}',";
            }
        }

        return implode("\n", $lines);
    }

    protected function buildPhpDoc(array $fields): string
    {
        $lines = ['/**', ' * @property int $id'];

        foreach ($fields as $field) {
            $type = match ($field['type']) {
                'string', 'char', 'text', 'mediumText', 'longText', 'uuid', 'ipAddress', 'macAddress' => 'string',
                'float', 'double', 'decimal' => 'float',
                'int', 'integer', 'bigint', 'tinyInteger', 'smallInteger', 'mediumInteger', 'unsignedBigInteger' => 'int',
                'bool', 'boolean' => 'bool',
                'date', 'datetime', 'timestamp', 'time', 'year' => '\Illuminate\Support\Carbon',
                'array', 'json' => 'array',
                default => 'mixed',
            };

            $lines[] = " * @property {$type} \${$field['name']}";
        }

        $lines[] = " * @property \Illuminate\Support\Carbon|null \$created_at";
        $lines[] = " * @property \Illuminate\Support\Carbon|null \$updated_at";
        $lines[] = ' */';

        return implode("\n", $lines);
    }
}
