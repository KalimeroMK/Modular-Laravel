<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class ListenerGenerator
{
    public function __construct(protected Filesystem $files) {}

    /**
     * @throws FileNotFoundException
     */
    public function generate(string $moduleName): void
    {
        $listeners = [
            'Created' => 'ModelCreatedListener',
            'Updated' => 'ModelUpdatedListener',
            'Deleted' => 'ModelDeletedListener',
        ];

        foreach ($listeners as $eventType => $stubName) {
            $className = $moduleName.$eventType.'Listener';
            $path = app_path("Modules/{$moduleName}/Application/Listeners/{$className}.php");
            $stubPath = base_path("stubs/module/Listeners/{$stubName}.stub");

            if (! $this->files->exists($stubPath)) {
                continue;
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
}
