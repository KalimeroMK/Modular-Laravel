<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class DTOGenerator
{
    public function __construct(protected Filesystem $files) {}

    /**
     * @param array<int, array{name: string, type: string, references?: string, on?: string}> $fields
     * @throws FileNotFoundException
     */
    public function generate(string $moduleName, array $fields): void
    {
        $path = app_path("Modules/{$moduleName}/Http/DTOs/{$moduleName}DTO.php");
        $stubPath = base_path('stubs/module/Http/DTOs/DTO.stub');

        if (! $this->files->exists($stubPath)) {
            return;
        }

        $constructorArgs = implode(",\n        ", array_map(function ($f) {
            $phpType = $this->mapToPhpType($f['type']);

            return "public ?{$phpType} \${$f['name']}";
        }, $fields));

        $fromArrayArgs = implode(",\n            ", array_map(fn ($f) => "\$data['{$f['name']}'] ?? null", $fields));
        $toArrayBody = implode(",\n            ", array_map(fn ($f) => "'{$f['name']}' => \$this->{$f['name']}", $fields));

        $replacements = [
            '{{module}}' => $moduleName,
            '{{constructor_args}}' => $constructorArgs,
            '{{from_array_args}}' => $fromArrayArgs,
            '{{to_array_body}}' => $toArrayBody,
        ];

        $content = $this->files->get($stubPath);
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);

        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $content);
    }

    protected function mapToPhpType(string $type): string
    {
        return match ($type) {
            'int', 'integer', 'bigint', 'tinyInteger', 'smallInteger', 'mediumInteger', 'unsignedBigInteger', 'foreign' => 'int',
            'float', 'double', 'decimal' => 'float',
            'bool', 'boolean' => 'bool',
            'array', 'json' => 'array',
            'uuid', 'char', 'string', 'text', 'mediumText', 'longText', 'enum', 'ipAddress', 'macAddress', 'binary', 'date', 'datetime', 'timestamp', 'time', 'year' => 'string',
            default => 'mixed',
        };
    }
}
