<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class ExceptionGenerator
{
    public function __construct(protected Filesystem $files) {}

    /**
     * @throws FileNotFoundException
     */
    public function generate(string $moduleName): void
    {
        $types = ['Store', 'Update', 'Delete', 'NotFound', 'Index'];
        $basePath = app_path("Modules/{$moduleName}/Application/Exceptions");
        $stubPath = base_path('stubs/module/Http/Exceptions/Exception.stub');

        if (! $this->files->exists($stubPath)) {
            return;
        }

        foreach ($types as $type) {
            $className = $moduleName.$type.'Exception';
            $filePath = $basePath."/{$className}.php";

            $replacements = [
                '{{module}}' => $moduleName,
                '{{exception}}' => $className,
            ];

            $content = $this->files->get($stubPath);
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);

            $this->files->ensureDirectoryExists(dirname($filePath));
            $this->files->put($filePath, $content);
        }
    }
}
