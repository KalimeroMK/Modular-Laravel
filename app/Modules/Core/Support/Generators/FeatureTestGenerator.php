<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class FeatureTestGenerator
{
    public function __construct(protected Filesystem $files) {}

    /**
     * @param array<int, array{name: string, type: string, references?: string, on?: string}> $fields
     */
    public function generate(string $moduleName, array $fields): void
    {
        $path = base_path("tests/Feature/Modules/{$moduleName}/{$moduleName}CrudTest.php");
        $stubPath = base_path('stubs/module/Tests/Feature/CrudTest.stub');

        if (! $this->files->exists($stubPath)) {
            return;
        }

        $storeData = $this->buildTestData($fields, false);
        $updateData = $this->buildTestData($fields, true);

        $content = $this->files->get($stubPath);
        $content = str_replace(
            ['{{module}}', '{{module_lower}}', '{{store_data}}', '{{update_data}}', '{{related_factories}}'],
            [
                $moduleName,
                Str::lower($moduleName),
                $storeData,
                $updateData,
                $this->buildRelatedFactories($fields),
            ],
            $content
        );

        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $content);
    }

    /**
     * @param array<int, array{name: string, type: string, references?: string, on?: string}> $fields
     */
    protected function buildTestData(array $fields, bool $forUpdate = false): string
    {
        $lines = [];

        foreach ($fields as $field) {
            if ($field['type'] === 'foreign') {
                continue; // handled separately
            }

            $value = match ($field['type']) {
                'string', 'text', 'char' => "'test'",
                'float', 'decimal', 'double' => 99.99,
                'int', 'integer', 'bigint' => 123,
                'bool', 'boolean' => 'true',
                'array', 'json' => "['key' => 'value']",
                default => "'sample'",
            };

            if ($forUpdate && $field['name'] === 'title') {
                $value = "'updated title'";
            }

            $lines[] = "            '{$field['name']}' => {$value},";
        }

        return implode("\n", $lines);
    }

    /**
     * @param array<int, array{name: string, type: string, references?: string, on?: string}> $fields
     */
    protected function buildRelatedFactories(array $fields): string
    {
        $lines = [];

        foreach ($fields as $field) {
            if ($field['type'] === 'foreign') {
                $model = Str::studly(Str::before($field['name'], '_id'));
                $lines[] = "        '{$field['name']}' => \App\Models\\{$model}::factory()->create()->id,";
            }
        }

        return "[\n" . implode("\n", $lines) . "\n    ]";
    }
}
