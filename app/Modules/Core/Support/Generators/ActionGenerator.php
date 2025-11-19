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
    public function generate(string $moduleName, bool $withEvents = false): void
    {
        $types = ['Create', 'Update', 'Delete', 'GetAll', 'GetById'];
        $basePath = app_path("Modules/{$moduleName}/Application/Actions");

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
                '{{moduleVar}}' => mb_strtolower($moduleName),
            ];

            // Add event-related replacements if events are enabled
            if ($withEvents) {
                $moduleVar = mb_strtolower($moduleName);
                $eventReplacements = [
                    '{{event_imports}}' => "use App\\Modules\\{$moduleName}\\Application\\Events\\{$moduleName}Created;\nuse App\\Modules\\{$moduleName}\\Application\\Events\\{$moduleName}Updated;\nuse App\\Modules\\{$moduleName}\\Application\\Events\\{$moduleName}Deleted;\nuse Illuminate\\Support\\Facades\\Event;",
                    '{{event_dispatch_created}}' => "Event::dispatch(new {$moduleName}Created(\$result));",
                    '{{event_dispatch_updated}}' => "Event::dispatch(new {$moduleName}Updated(\$result));",
                    '{{event_dispatch_deleting}}' => '',
                    '{{event_dispatch_deleted}}' => "Event::dispatch(new {$moduleName}Deleted(\${$moduleVar}));",
                ];
                $replacements = array_merge($replacements, $eventReplacements);
            } else {
                $replacements['{{event_imports}}'] = '';
                $replacements['{{event_dispatch_created}}'] = '';
                $replacements['{{event_dispatch_updated}}'] = '';
                $replacements['{{event_dispatch_deleting}}'] = '';
                $replacements['{{event_dispatch_deleted}}'] = '';
            }

            $content = $this->files->get($stubPath);
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);

            $this->files->ensureDirectoryExists(dirname($filePath));
            $this->files->put($filePath, $content);
        }
    }
}
