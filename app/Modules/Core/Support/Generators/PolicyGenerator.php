<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class PolicyGenerator
{
    public function __construct(protected Filesystem $files) {}

    /**
     * @throws FileNotFoundException
     */
    public function generate(string $moduleName): void
    {
        $className = $moduleName.'Policy';
        $path = app_path("Modules/{$moduleName}/Policies/{$className}.php");
        $stubPath = base_path('stubs/module/Policies/ModelPolicy.stub');

        if (! $this->files->exists($stubPath)) {
            return;
        }

        $replacements = [
            '{{module}}' => $moduleName,
            '{{policy}}' => $className,
        ];

        $content = $this->files->get($stubPath);
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);

        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $content);
    }
}
