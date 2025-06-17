<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class StubFileGenerator
{
    public function __construct(protected Filesystem $files) {}

    /**
     * @throws FileNotFoundException
     */
    public function generate(string $moduleName, array $fields, array $options): void
    {
        $basePath = app_path("Modules/{$moduleName}");
        $replacements = [
            '{{module}}' => $moduleName,
            '{{module_lower}}' => mb_strtolower($moduleName),
            '{{moduleVar}}' => mb_strtolower($moduleName),
            '{{table}}' => $options['table'] ?? \Str::plural(\Str::snake($moduleName)),
            '{{timestamp}}' => now()->format('Y_m_d_His'),
        ];

        $stubMap = [
            'Interfaces/{{module}}Interface.php' => 'stubs/module/Interface.stub',
            'Repositories/{{module}}Repository.php' => 'stubs/module/Repository.stub',
            'Models/{{module}}.php' => 'stubs/module/Model.stub',
            'database/factories/{{module}}Factory.php' => 'stubs/module/Factory.stub',
            'routes/api.php' => 'stubs/module/routes/api.stub',
            'Http/Controllers/{{module}}Controller.php' => 'stubs/module/Http/Controllers/Controller.stub',
            'Http/Resources/{{module}}Resource.php' => 'stubs/module/Http/Resource/Resource.stub',
            'database/migrations/{{timestamp}}_create_{{table}}_table.php' => 'stubs/module/Migration.stub',
        ];

        foreach ($stubMap as $target => $stubPath) {
            $targetPath = $basePath.'/'.str_replace(array_keys($replacements), array_values($replacements), $target);
            $stubFullPath = base_path($stubPath);

            if (! $this->files->exists($stubFullPath)) {
                continue;
            }

            $currentReplacements = $replacements;

            if (Str::endsWith($stubPath, 'Factory.stub')) {
                $currentReplacements['{{factory_fields}}'] = $this->buildFactoryFields($fields);
            }

            if (Str::endsWith($stubPath, 'Migration.stub')) {
                $currentReplacements['{{migration_fields}}'] = $this->buildMigrationFields($fields);
            }

            if (Str::endsWith($stubPath, 'Resource.stub')) {
                $currentReplacements['{{resource_fields}}'] = $this->buildResourceFields($fields);
            }

            if (Str::endsWith($stubPath, 'Model.stub')) {
                $currentReplacements['{{table}}'] = $options['table'] ?? $replacements['{{table}}'];
                $currentReplacements['{{fillable}}'] = implode(', ', array_map(fn($f) => "'{$f['name']}'", $fields));
                $currentReplacements['{{casts}}'] = $this->buildCasts($fields);
                $currentReplacements['{{phpdoc_block}}'] = $this->buildPhpDoc($fields);
                $currentReplacements['{{relationships}}'] = $options['relationships'] ?? '';
            }

            $content = $this->files->get($stubFullPath);
            $content = str_replace(array_keys($currentReplacements), array_values($currentReplacements), $content);

            $this->files->ensureDirectoryExists(dirname($targetPath));
            $this->files->put($targetPath, $content);
        }
    }

    protected function buildFactoryFields(array $fields): string { return ''; }
    protected function buildMigrationFields(array $fields): string { return ''; }
    protected function buildResourceFields(array $fields): string { return ''; }
    protected function buildCasts(array $fields): string { return ''; }
    protected function buildPhpDoc(array $fields): string { return ''; }
}
