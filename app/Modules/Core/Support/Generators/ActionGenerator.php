<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class ActionGenerator
{
    public function __construct(protected Filesystem $files) {}

    /**
     * @throws FileNotFoundException
     */
    public function generate(string $moduleName): void
    {
        $types = ['Create', 'Update', 'Delete', 'GetAll', 'GetById'];
        $basePath = app_path("Modules/{$moduleName}/Http/Actions");

        foreach ($types as $type) {
            $className = $type.$moduleName.'Action';
            $filePath = $basePath."/{$className}.php";
            $stubPath = base_path("stubs/module/Http/Actions/{$type}Action.stub");

            if (! $this->files->exists($stubPath)) {
                continue;
            }

            $replacements = [
                '{{module}}' => $moduleName,
                '{{class}}' => $moduleName,
            ];

            $content = $this->files->get($stubPath);
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);

            $this->files->ensureDirectoryExists(dirname($filePath));
            $this->files->put($filePath, $content);
        }
    }
}
