<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class RequestGenerator
{
    public function __construct(protected Filesystem $files) {}

    /**
     * @throws FileNotFoundException
     */
    public function generate(string $moduleName, array $fields): void
    {
        foreach (['Create', 'Update'] as $type) {
            $className = $type.$moduleName.'Request';
            $path = app_path("Modules/{$moduleName}/Http/Requests/{$className}.php");
            $stubPath = base_path("stubs/module/Http/Requests/{$type}Request.stub");

            if (! $this->files->exists($stubPath)) {
                continue;
            }

            $rules = implode("\n", array_map(function ($f) {
                $ruleType = match ($f['type']) {
                    'int' => 'integer',
                    'float' => 'numeric',
                    'bool' => 'boolean',
                    'array' => 'array',
                    default => 'string'
                };
                $required = $f['name'] === 'id' ? '' : "'required', ";

                return "    '{$f['name']}' => [{$required}'{$ruleType}'],";
            }, array_filter($fields, fn ($f) => $f['name'] !== 'id')));

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
