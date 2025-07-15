<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class RequestGenerator
{
    public function __construct(protected Filesystem $files) {}

    /**
     * @param array<int, array{name: string, type: string, references?: string, on?: string}> $fields
     * @throws FileNotFoundException
     */
    public function generate(string $moduleName, array $fields): void
    {
        foreach (['Create', 'Update'] as $type) {
            $className = $type . $moduleName . 'Request';
            $path = app_path("Modules/{$moduleName}/Http/Requests/{$className}.php");
            $stubPath = base_path("stubs/module/Http/Requests/{$type}Request.stub");

            if (! $this->files->exists($stubPath)) {
                continue;
            }

            $rules = implode("\n", array_filter(array_map(function ($field) {
                if (!isset($field['name']) || !isset($field['type']) || $field['name'] === 'id') {
                    return null;
                }

                if ($field['type'] === 'foreignId' && (!isset($field['references']) || !isset($field['on']))) {
                    return null;
                }

                $type = $field['type'];
                $name = $field['name'];

                $rule = match ($type) {
                    'int', 'integer', 'bigint', 'tinyInteger', 'smallInteger', 'mediumInteger', 'unsignedBigInteger' => 'integer',
                    'float', 'double', 'decimal' => 'numeric',
                    'bool', 'boolean' => 'boolean',
                    'array', 'json' => 'array',
                    'foreign' => 'integer|exists:' . ($field['on'] ?? 'users') . ',' . ($field['references'] ?? 'id'),
                    default => 'string'
                };

                return "            '{$name}' => ['required', '{$rule}'],";
            }, $fields)));

            $replacements = [
                '{{module}}' => $moduleName,
                '{{validation_rules}}' => $rules,
            ];

            $content = $this->files->get($stubPath);
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);

            $this->files->ensureDirectoryExists(dirname($path));
            $this->files->put($path, $content);
        }
    }
}
