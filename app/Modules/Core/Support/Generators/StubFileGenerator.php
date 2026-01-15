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
     * @param  array<int, array{name: string, type: string, references?: string, on?: string}>  $fields
     * @param  array<string, mixed>  $options
     *
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
            'Infrastructure/Repositories/{{module}}RepositoryInterface.php' => 'stubs/module/Interface.stub',
            'Infrastructure/Repositories/{{module}}Repository.php' => 'stubs/module/Repository.stub',
            'Infrastructure/Models/{{module}}.php' => 'stubs/module/Model.stub',
            'Database/Factories/{{module}}Factory.php' => 'stubs/module/Factory.stub',
            'Infrastructure/Routes/{{module_lower}}.php' => 'stubs/module/routes/api.stub',
            'Infrastructure/Http/Controllers/{{module}}Controller.php' => 'stubs/module/Http/Controllers/Controller.stub',
            'Infrastructure/Http/Resources/{{module}}Resource.php' => 'stubs/module/Http/Resource/Resource.stub',
            'Infrastructure/Providers/{{module}}ModuleServiceProvider.php' => 'stubs/module/ServiceProvider.stub',
            'Database/migrations/{{timestamp}}_create_{{table}}_table.php' => 'stubs/module/Migration.stub',
        ];

        foreach ($stubMap as $target => $stubPath) {
            // Replace placeholders in target path - replace longer placeholders first to avoid conflicts
            $targetPath = $target;
            foreach ($replacements as $placeholder => $value) {
                $targetPath = str_replace($placeholder, $value, $targetPath);
            }
            $targetPath = $basePath.'/'.$targetPath;
            $stubFullPath = base_path($stubPath);

            if (! $this->files->exists($stubFullPath)) {
                continue;
            }

            // Skip if file already exists (for existing modules, only generate missing files)
            // Exception: migrations should always be generated with timestamp
            if ($this->files->exists($targetPath) && ! str_contains($targetPath, 'migrations')) {
                continue;
            }

            $currentReplacements = $replacements;

            if (Str::endsWith($stubPath, 'Factory.stub')) {
                $currentReplacements['{{factory_fields}}'] = $this->buildFactoryFields($fields);
            }

            if (Str::endsWith($stubPath, 'Migration.stub')) {
                $currentReplacements['{{migration_fields}}'] = $this->buildMigrationFields($fields);
                $currentReplacements['{{migration_indexes}}'] = $this->buildMigrationIndexes($fields);
            }

            if (Str::endsWith($stubPath, 'Resource.stub')) {
                $currentReplacements['{{resource_fields}}'] = $this->buildResourceFields($fields);
            }

            if (Str::endsWith($stubPath, 'Model.stub')) {
                $currentReplacements['{{table}}'] = $replacements['{{table}}'];
                $currentReplacements['{{fillable}}'] = $this->buildFillableFields($fields);
                $currentReplacements['{{casts}}'] = $this->buildCasts($fields);
                $currentReplacements['{{phpdoc_block}}'] = $this->buildPhpDoc($fields);
                $currentReplacements['{{phpdoc_properties}}'] = $this->buildPhpDocProperties($fields);

                // Parse relationships and imports
                $relationships = $options['relationships'] ?? '';
                $relationshipMethods = $this->extractRelationshipMethods($relationships);
                $relationshipImports = $this->extractRelationshipImports($relationships);

                $currentReplacements['{{relationships}}'] = $relationshipMethods;
                $currentReplacements['{{relationship_imports}}'] = $relationshipImports;
            }

            $content = $this->files->get($stubFullPath);
            $content = str_replace(array_keys($currentReplacements), array_values($currentReplacements), $content);

            $this->files->ensureDirectoryExists(dirname($targetPath));
            $this->files->put($targetPath, $content);

            // Track generated file for rollback
            if (isset($options['tracker']) && $options['tracker'] instanceof ModuleGenerationTracker) {
                $options['tracker']->trackGeneratedFile($moduleName, $targetPath);
            }
        }
    }

    /**
     * @param  array<int, array{name: string, type: string, references?: string, on?: string}>  $fields
     */
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

    /**
     * @param  array<int, array{name: string, type: string, references?: string, on?: string, morphable_name?: string}>  $fields
     */
    protected function buildMigrationFields(array $fields): string
    {
        $lines = [];
        $addedMorphs = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $field['type'];

            if ($type === 'foreign') {
                $references = $field['references'] ?? 'id';
                $on = $field['on'] ?? 'users';
                $lines[] = "            \$table->foreignId('{$name}')->constrained('{$on}')->references('{$references}');";
            } elseif (isset($field['morphable_name'])) {
                // Handle morphable fields - generate morphs() instead of individual type/id columns
                $morphableName = $field['morphable_name'];
                if (! in_array($morphableName, $addedMorphs)) {
                    $lines[] = "            \$table->morphs('{$morphableName}');";
                    $addedMorphs[] = $morphableName;
                }
                // Skip individual _type and _id fields since morphs() handles both
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

    /**
     * Build migration indexes for performance optimization.
     *
     * @param  array<int, array{name: string, type: string, references?: string, on?: string}>  $fields
     */
    protected function buildMigrationIndexes(array $fields): string
    {
        $indexes = [];
        $hasForeignKeys = false;
        $indexableFields = [];

        // Always index timestamps for common queries
        $indexes[] = "            \$table->index('created_at');";
        $indexes[] = "            \$table->index('updated_at');";

        // Index foreign keys (they're already indexed by constrained, but add explicit index for clarity)
        foreach ($fields as $field) {
            if ($field['type'] === 'foreign') {
                $hasForeignKeys = true;
                $indexableFields[] = $field['name'];
            }
        }

        // Index common query fields (status, is_active, etc.)
        $commonIndexablePatterns = ['status', 'is_active', 'is_enabled', 'active', 'enabled', 'published', 'visible'];

        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $field['type'];

            // Index boolean fields that are commonly queried
            if (in_array($type, ['boolean', 'bool']) && in_array($name, $commonIndexablePatterns)) {
                $indexes[] = "            \$table->index('{$name}');";
            }

            // Index status/enum fields
            if ($type === 'enum' || in_array($name, ['status', 'type', 'state'])) {
                $indexes[] = "            \$table->index('{$name}');";
            }

            // Index date/timestamp fields that might be queried
            if (in_array($type, ['date', 'datetime', 'timestamp']) &&
                ! in_array($name, ['created_at', 'updated_at'])) {
                $indexes[] = "            \$table->index('{$name}');";
            }
        }

        // Add composite index for foreign key + status if both exist
        if ($hasForeignKeys && $indexableFields !== []) {
            foreach ($indexableFields as $fkField) {
                // Check if there's a status field
                foreach ($fields as $field) {
                    if (in_array($field['name'], ['status', 'is_active', 'active'])) {
                        $indexes[] = "            \$table->index(['{$fkField}', '{$field['name']}']);";
                        break;
                    }
                }
            }
        }

        return implode("\n", $indexes);
    }

    /**
     * @param  array<int, array{name: string, type: string}>  $fields
     */
    protected function buildResourceFields(array $fields): string
    {
        $lines = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $lines[] = "            '{$name}' => \$this->{$name},";
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<int, array{name: string, type: string}>  $fields
     */
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

    /**
     * @param  array<int, array{name: string, type: string, morphable_name?: string}>  $fields
     */
    protected function buildFillableFields(array $fields): string
    {
        // Include all field names in fillable, including morphable fields
        return implode(', ', array_map(fn ($f) => "'{$f['name']}'", $fields));
    }

    /**
     * @param  array<int, array{name: string, type: string}>  $fields
     */
    protected function buildPhpDoc(array $fields): string
    {
        $lines = ['/**', ' * @property int $id'];

        foreach ($fields as $field) {
            $type = match ($field['type']) {
                'string', 'char', 'text', 'mediumText', 'longText', 'uuid', 'ipAddress', 'macAddress' => 'string',
                'float', 'double', 'decimal' => 'float',
                'int', 'integer', 'bigint', 'tinyInteger', 'smallInteger', 'mediumInteger', 'unsignedBigInteger' => 'int',
                'bool', 'boolean' => 'bool',
                'date', 'datetime', 'timestamp', 'time', 'year' => \Illuminate\Support\Carbon::class,
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

    /**
     * Build PHPDoc properties for model class.
     *
     * @param  array<int, array{name: string, type: string}>  $fields
     */
    protected function buildPhpDocProperties(array $fields): string
    {
        $lines = [];

        foreach ($fields as $field) {
            $type = match ($field['type']) {
                'string', 'char', 'text', 'mediumText', 'longText', 'uuid', 'ipAddress', 'macAddress' => 'string',
                'float', 'double', 'decimal' => 'float',
                'int', 'integer', 'bigint', 'tinyInteger', 'smallInteger', 'mediumInteger', 'unsignedBigInteger' => 'int',
                'bool', 'boolean' => 'bool',
                'date', 'datetime', 'timestamp', 'time', 'year' => '\Illuminate\Support\Carbon|null',
                'array', 'json' => 'array<string, mixed>',
                default => 'mixed',
            };

            // Add nullable for optional fields
            $nullable = in_array($field['type'], ['date', 'datetime', 'timestamp', 'time', 'year']) ? '|null' : '';
            $lines[] = " * @property {$type}{$nullable} \${$field['name']}";
        }

        return implode("\n", $lines);
    }

    /**
     * Extract relationship methods from relationships string
     */
    protected function extractRelationshipMethods(string $relationships): string
    {
        if ($relationships === '' || $relationships === '0') {
            return '';
        }

        $lines = explode("\n", $relationships);
        $methods = [];
        $currentMethod = '';
        $inMethod = false;
        $braceCount = 0;

        foreach ($lines as $line) {
            $line = mb_trim($line);

            // Skip import statements
            if (str_starts_with($line, 'use ')) {
                continue;
            }

            // Start of a method
            if (str_contains($line, 'public function')) {
                if ($inMethod && $currentMethod) {
                    $methods[] = $currentMethod;
                }
                $currentMethod = $line;
                $inMethod = true;
                $braceCount = mb_substr_count($line, '{') - mb_substr_count($line, '}');
            } elseif ($inMethod) {
                $currentMethod .= "\n".$line;
                $braceCount += mb_substr_count($line, '{') - mb_substr_count($line, '}');

                // Method is complete when braces are balanced
                if ($braceCount <= 0) {
                    $methods[] = $currentMethod;
                    $currentMethod = '';
                    $inMethod = false;
                    $braceCount = 0;
                }
            }
        }

        // Add the last method if it exists
        if ($inMethod && $currentMethod) {
            $methods[] = $currentMethod;
        }

        return implode("\n\n", $methods);
    }

    /**
     * Extract relationship imports from relationships string
     */
    protected function extractRelationshipImports(string $relationships): string
    {
        if ($relationships === '' || $relationships === '0') {
            return '';
        }

        $imports = [];
        foreach (explode("\n", $relationships) as $line) {
            if (str_starts_with($line, 'use ')) {
                $imports[] = $line;
            }
        }

        return implode("\n", $imports);
    }
}
