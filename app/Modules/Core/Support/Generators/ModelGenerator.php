<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class ModelGenerator
{
    public function __construct(protected Filesystem $files) {}

    /**
     * @throws FileNotFoundException
     */
    public function generate(string $moduleName, array $fields, array $options): void
    {
        $path = app_path("Modules/{$moduleName}/Models/{$moduleName}.php");
        $stubPath = base_path('stubs/module/Model.stub');

        if (! $this->files->exists($stubPath)) {
            return;
        }

        $fillable = implode(', ', array_map(fn ($f) => "'{$f['name']}'", $fields));

        $casts = implode("\n", array_filter(array_map(function ($f) {
            return match ($f['type']) {
                'int', 'float', 'bool', 'array' => "        '{$f['name']}' => '{$f['type']}',",
                default => null,
            };
        }, $fields)));

        $phpdoc = "/**\n * @property int \$id\n".implode("\n", array_map(function ($f) {
            $type = match ($f['type']) {
                'int' => 'int', 'float' => 'float', 'bool' => 'bool', 'array' => 'array', default => 'string'
            };

            return " * @property {$type} \${$f['name']}";
        }, $fields))."\n * @property \\Illuminate\\Support\\Carbon|null \$created_at\n * @property \\Illuminate\\Support\\Carbon|null \$updated_at\n */";

        $replacements = [
            '{{module}}' => $moduleName,
            '{{fillable}}' => $fillable,
            '{{casts}}' => $casts,
            '{{phpdoc_block}}' => $phpdoc,
            '{{relationships}}' => $this->buildRelationships($options['relations'] ?? ''),
        ];

        $content = $this->files->get($stubPath);
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);

        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $content);
    }

    protected function buildRelationships(string $relationString): string
    {
        if (! $relationString) {
            return '';
        }

        $lines = [];
        foreach (explode(',', $relationString) as $relation) {
            [$name, $type, $related] = array_pad(explode(':', $relation), 3, null);
            if (! $name || ! $type || ! $related) {
                continue;
            }
            $lines[] = "    public function {$name}()\n    {\n        return \$this->{$type}({$related}::class);\n    }\n";
        }

        return "\n".implode("\n", $lines);
    }
}
