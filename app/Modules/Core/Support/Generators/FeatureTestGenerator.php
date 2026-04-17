<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class FeatureTestGenerator
{
    public function __construct(protected Filesystem $files) {}

    public function generate(string $moduleName, array $fields, array $options = []): void
    {
        $path = base_path("tests/Feature/Modules/{$moduleName}/{$moduleName}CrudTest.php");
        $stubPath = base_path('stubs/module/Tests/Feature/CrudTest.stub');

        if (! $this->files->exists($stubPath)) {
            return;
        }

        $tableName = $options['table'] ?? Str::plural(Str::snake($moduleName));
        $storeData = $this->buildTestData($fields, false);
        $updateData = $this->buildTestData($fields, true);

        $content = $this->files->get($stubPath);
        $content = str_replace(
            ['{{module}}', '{{module_lower}}', '{{table}}', '{{store_data}}', '{{update_data}}', '{{related_factories}}'],
            [
                $moduleName,
                Str::lower($moduleName),
                $tableName,
                $storeData,
                $updateData,
                $this->buildRelatedFactories($fields),
            ],
            $content
        );

        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $content);
    }

    protected function buildTestData(array $fields, bool $forUpdate = false): string
    {
        $lines = [];

        foreach ($fields as $field) {
            if ($field['type'] === 'foreign') {
                continue;
            }

            $fieldName = $field['name'];
            $prefix = $forUpdate ? 'Updated' : 'Test';

            $value = match ($field['type']) {
                'string', 'text', 'char' => "'{$prefix} {$fieldName}'",
                'float', 'decimal', 'double' => $forUpdate ? '99.99' : '123.45',
                'int', 'integer', 'bigint' => $forUpdate ? '456' : '123',
                'bool', 'boolean' => $forUpdate ? 'false' : 'true',
                'array', 'json' => "['key' => 'value']",
                'date' => "'2023-01-01'",
                'datetime', 'timestamp' => "'2023-01-01 12:00:00'",
                'time' => "'12:00:00'",
                'email' => $forUpdate ? "'updated@example.com'" : "'test@example.com'",
                default => "'{$prefix} {$fieldName}'",
            };

            if (Str::contains($fieldName, 'email')) {
                $value = $forUpdate ? "'updated@example.com'" : "'test@example.com'";
            } elseif (Str::contains($fieldName, 'name')) {
                $value = "'{$prefix} ".Str::title(str_replace('_', ' ', $fieldName))."'";
            } elseif (Str::contains($fieldName, 'password')) {
                $value = "'password123'";
            } elseif ($fieldName === 'guard_name') {
                $value = "'api'";
            }

            $lines[] = "            '{$fieldName}' => {$value},";
        }

        return implode("\n", $lines);
    }

    protected function buildRelatedFactories(array $fields): string
    {
        $lines = [];

        foreach ($fields as $field) {
            if ($field['type'] === 'foreign') {
                $modelName = $this->getModelNameFromForeign($field);
                $modelPath = $this->getModelPath($modelName);
                $lines[] = "            '{$field['name']}' => {$modelPath}::factory()->create()->id,";
            }
        }

        if ($lines === []) {
            return '[]';
        }

        return "[\n".implode("\n", $lines)."\n        ]";
    }

    protected function getModelNameFromForeign(array $field): string
    {
        if (isset($field['on'])) {
            return Str::studly(Str::singular($field['on']));
        }

        $fieldName = $field['name'];
        if (Str::endsWith($fieldName, '_id')) {
            return Str::studly(Str::before($fieldName, '_id'));
        }

        return Str::studly($fieldName);
    }

    protected function getModelPath(string $modelName): string
    {

        $standardModels = ['User', 'Role', 'Permission'];
        if (in_array($modelName, $standardModels)) {
            return "\\App\\Modules\\{$modelName}\\Infrastructure\\Models\\{$modelName}";
        }

        $modulePath = app_path("Modules/{$modelName}/Infrastructure/Models/{$modelName}.php");
        if (file_exists($modulePath)) {
            return "\\App\\Modules\\{$modelName}\\Infrastructure\\Models\\{$modelName}";
        }

        return "\\App\\Models\\{$modelName}";
    }
}
