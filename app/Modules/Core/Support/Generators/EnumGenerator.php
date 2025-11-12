<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class EnumGenerator
{
    public function __construct(protected Filesystem $files) {}

    /**
     * Generate Enum class for a module.
     *
     * @throws FileNotFoundException
     */
    public function generate(string $moduleName): void
    {
        $path = app_path("Modules/{$moduleName}/Enums/{$moduleName}Status.php");
        $stubPath = base_path('stubs/module/Enum.stub');

        if (! $this->files->exists($stubPath)) {
            return;
        }

        $replacements = [
            '{{module}}' => $moduleName,
        ];

        $content = $this->files->get($stubPath);
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);

        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $content);
    }
}
