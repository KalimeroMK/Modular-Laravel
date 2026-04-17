<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class ActionGenerator
{
    public function __construct(protected Filesystem $files) {}

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

            if ($withEvents) {
                $eventReplacements = $this->buildEventReplacements($moduleName, $type);
                $replacements = array_merge($replacements, $eventReplacements);
            } else {
                $replacements['{{event_imports}}'] = '';
                $replacements['{{event_method_created}}'] = '';
                $replacements['{{event_method_updated}}'] = '';
                $replacements['{{event_method_deleted}}'] = '';
            }

            $content = $this->files->get($stubPath);
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);

            $this->files->ensureDirectoryExists(dirname($filePath));
            $this->files->put($filePath, $content);
        }
    }

    protected function buildEventReplacements(string $moduleName, string $type): array
    {
        $baseImport = "use App\\Modules\\{$moduleName}\\Application\\Events\\";
        $eventClass = match ($type) {
            'Create' => "{$moduleName}Created",
            'Update' => "{$moduleName}Updated",
            'Delete' => "{$moduleName}Deleted",
            default => null,
        };

        if ($eventClass === null) {
            return [
                '{{event_imports}}' => '',
                '{{event_method_created}}' => '',
                '{{event_method_updated}}' => '',
                '{{event_method_deleted}}' => '',
            ];
        }

        $imports = "use Illuminate\\Support\\Facades\\Event;\n{$baseImport}{$eventClass};";

        $method = match ($type) {
            'Create' => "    protected function afterCreate(\\Illuminate\\Database\\Eloquent\\Model \$model): void\n    {\n        Event::dispatch(new {$eventClass}(\$model));\n    }",
            'Update' => "    protected function afterUpdate(\\Illuminate\\Database\\Eloquent\\Model \$model): void\n    {\n        Event::dispatch(new {$eventClass}(\$model));\n    }",
            'Delete' => "    protected function beforeDelete(\\Illuminate\\Database\\Eloquent\\Model \$entity, int|string \$id): void\n    {\n        Event::dispatch(new {$eventClass}(\$entity));\n    }",
            default => '',
        };

        return [
            '{{event_imports}}' => $imports,
            '{{event_method_created}}' => $type === 'Create' ? $method : '',
            '{{event_method_updated}}' => $type === 'Update' ? $method : '',
            '{{event_method_deleted}}' => $type === 'Delete' ? $method : '',
        ];
    }
}
