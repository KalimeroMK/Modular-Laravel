<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class EventGenerator
{
    public function __construct(protected Filesystem $files) {}

    /**
     * @throws FileNotFoundException
     */
    public function generate(string $moduleName): void
    {
        $modelName = Str::camel($moduleName);
        $events = ['Created', 'Updated', 'Deleted'];

        foreach ($events as $eventType) {
            $className = $moduleName.$eventType;
            $path = app_path("Modules/{$moduleName}/Application/Events/{$className}.php");
            $stubPath = base_path("stubs/module/Events/Model{$eventType}.stub");

            if (! $this->files->exists($stubPath)) {
                continue;
            }

            $replacements = [
                '{{module}}' => $moduleName,
                '{{model}}' => $modelName,
            ];

            $content = $this->files->get($stubPath);
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);

            $this->files->ensureDirectoryExists(dirname($path));
            $this->files->put($path, $content);
        }
    }
}
