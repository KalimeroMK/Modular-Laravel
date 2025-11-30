<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class RequestGenerator
{
    public function __construct(protected Filesystem $files) {}

    /**
     * @param  array<int, array{name: string, type: string, references?: string, on?: string}>  $fields
     *
     * @throws FileNotFoundException
     */
    public function generate(string $moduleName, array $fields): void
    {
        foreach (['Create', 'Update'] as $type) {
            $className = $type.$moduleName.'Request';
            $path = app_path("Modules/{$moduleName}/Infrastructure/Http/Requests/{$className}.php");
            $stubPath = base_path("stubs/module/Http/Requests/{$type}Request.stub");

            if (! $this->files->exists($stubPath)) {
                continue;
            }

            $moduleVar = \Illuminate\Support\Str::camel($moduleName);
            $moduleLower = \Illuminate\Support\Str::lower($moduleName);

            $rules = implode("\n", array_filter(array_map(function ($field) use ($type) {
                if ($field['name'] === 'id') {
                    return null;
                }

                if ($field['type'] === 'foreignId' && (! isset($field['references']) || ! isset($field['on']))) {
                    return null;
                }

                $fieldType = $field['type'];
                $name = $field['name'];

                $rule = match ($fieldType) {
                    'int', 'integer', 'bigint', 'tinyInteger', 'smallInteger', 'mediumInteger', 'unsignedBigInteger' => 'integer',
                    'float', 'double', 'decimal' => 'numeric',
                    'bool', 'boolean' => 'boolean',
                    'array', 'json' => 'array',
                    'foreign' => 'integer|exists:'.($field['on'] ?? 'users').','.($field['references'] ?? 'id'),
                    default => 'string'
                };

                // For Update requests, use 'sometimes' instead of 'required'
                $required = $type === 'Update' ? 'sometimes' : 'required';

                return "            '{$name}' => ['{$required}', '{$rule}'],";
            }, $fields)));

            // Generate validation messages
            $messages = implode("\n", array_filter(array_map(function ($field) {
                if ($field['name'] === 'id') {
                    return null;
                }

                if ($field['type'] === 'foreignId' && (! isset($field['references']) || ! isset($field['on']))) {
                    return null;
                }

                $fieldType = $field['type'];
                $name = $field['name'];

                $messageType = match ($fieldType) {
                    'int', 'integer', 'bigint', 'tinyInteger', 'smallInteger', 'mediumInteger', 'unsignedBigInteger' => 'integer',
                    'float', 'double', 'decimal' => 'numeric',
                    'bool', 'boolean' => 'boolean',
                    'array', 'json' => 'array',
                    default => 'string'
                };

                return "            '{$name}.{$messageType}' => 'The {$name} must be a {$messageType}.',";
            }, $fields)));

            $replacements = [
                '{{module}}' => $moduleName,
                '{{moduleVar}}' => $moduleVar,
                '{{module_lower}}' => $moduleLower,
                '{{validation_rules}}' => $rules,
                '{{validation_messages}}' => $messages,
            ];

            $content = $this->files->get($stubPath);
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);

            $this->files->ensureDirectoryExists(dirname($path));
            $this->files->put($path, $content);
        }
    }
}
