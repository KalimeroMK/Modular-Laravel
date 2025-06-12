<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name : The name of the module} {--migrations : Generate migration and model files with fields and relationships} {--model= : Model fields, e.g. name:string,coverImg:string,restaurant_id:foreignId} {--relationships= : Model relationships, e.g. restaurant:belongsTo,pictures:hasMany}';

    protected $description = 'Create a new API module with predefined structure and files';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle(): int
    {
        $moduleName = Str::studly($this->argument('name'));
        $modulePath = app_path("Modules/{$moduleName}");

        if ($this->files->exists($modulePath)) {
            $this->warn("Module '{$moduleName}' already exists. We'll only create missing files.");
        } else {
            $this->files->makeDirectory($modulePath, 0755, true);
        }

        try {
            $this->createModuleStructure($modulePath);
            $this->generateFiles($modulePath, $moduleName);
            $this->info("Module '{$moduleName}' is now up-to-date!");
        } catch (\Exception $e) {
            if (! $this->files->exists($modulePath)) {
                $this->cleanupModule($modulePath);
            }
            $this->error("Failed to update/create module: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }

    protected function createModuleStructure(string $modulePath): void
    {
        $structure = [
            'Http/DTOs', 'Http/Actions', 'Http/Controllers', 'Http/Requests',
            'Http/Resources', 'Models', 'Repositories', 'Interfaces',
            'database/migrations', 'database/factories', 'routes',
            'Config', 'Exceptions', 'Helpers', 'Traits',
        ];

        foreach ($structure as $directory) {
            $path = "$modulePath/$directory";
            if (! $this->files->exists($path)) {
                $this->files->makeDirectory($path, 0755, true);
            }
        }
    }

    protected function generateFiles(string $modulePath, string $moduleName): void
    {
        $tableName = Str::plural(Str::snake($moduleName));
        $fields = $this->parseModelFields($this->option('model'));

        $replacements = [
            '{{module}}' => $moduleName,
            '{{module_lower}}' => Str::lower($moduleName),
            '{{table}}' => $tableName,
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
        ];

        foreach ($stubMap as $target => $stubPath) {
            $outputPath = $modulePath.'/'.str_replace(array_keys($replacements), array_values($replacements), $target);
            $this->createFromStub($stubPath, $outputPath, $replacements);
        }

        $dtoStubPath = base_path('stubs/module/Http/DTOs/DTO.stub');
        foreach (['Create', 'Update'] as $type) {
            if ($this->files->exists($dtoStubPath)) {
                $dtoReplacements = array_merge(
                    $replacements,
                    ['{{type}}' => $type],
                    $this->buildDtoReplacements($fields)
                );
                $filePath = "$modulePath/Http/DTOs/{$type}{$moduleName}DTO.php";
                $this->createFromStub($dtoStubPath, $filePath, $dtoReplacements);
            }
        }

        $actionStubBase = base_path('stubs/module/Http/Actions');
        $actionMap = ['Create', 'Update', 'Delete', 'GetAll', 'GetById'];

        foreach ($actionMap as $action) {
            $stubPath = "$actionStubBase/{$action}Action.stub";
            if ($this->files->exists($stubPath)) {
                $actionReplacements = array_merge(
                    $replacements,
                    ['{{class}}' => $moduleName],
                    $this->buildActionReplacements($fields, $moduleName)
                );
                $filePath = "$modulePath/Http/Actions/{$action}{$moduleName}Action.php";
                $this->createFromStub($stubPath, $filePath, $actionReplacements);
            }
        }

        // Request classes
        $requestTypes = ['Create', 'Update', 'Delete', 'GetById', 'GetAll'];
        $requestStub = base_path('stubs/module/Http/Request/Request.stub');
        foreach ($requestTypes as $type) {
            if ($this->files->exists($requestStub)) {
                $requestReplacements = array_merge(
                    $replacements,
                    ['{{type}}' => $type, '{{validation_rules}}' => $this->buildValidationRules($fields)],
                );
                $filePath = "$modulePath/Http/Requests/{$type}{$moduleName}Request.php";
                $this->createFromStub($requestStub, $filePath, $requestReplacements);
            }
        }

        // Exception classes
        $exceptionTypes = ['Store', 'Update', 'Delete', 'NotFound', 'Index'];
        $exceptionStub = base_path('stubs/module/Http/Exceptions/Exception.stub');
        foreach ($exceptionTypes as $type) {
            if ($this->files->exists($exceptionStub)) {
                $exceptionReplacements = array_merge($replacements, ['{{type}}' => $type]);
                $filePath = "$modulePath/Exceptions/{$moduleName}{$type}Exception.php";
                $this->createFromStub($exceptionStub, $filePath, $exceptionReplacements);
            }
        }
    }

    protected function createFromStub(string $stubPath, string $outputPath, array $replacements): void
    {
        $content = $this->files->get(base_path($stubPath));
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);

        $this->files->ensureDirectoryExists(dirname($outputPath));
        $this->files->put($outputPath, $content);
        $this->info("Created file: {$outputPath}");
    }

    protected function buildActionReplacements(array $fields, string $moduleName): array
    {
        $createArray = implode(",\n            ", array_map(
            fn ($f) => "'{$f['name']}' => \$dto->{$f['name']}",
            $fields
        ));

        return [
            '{{create_array}}' => $createArray,
        ];
    }

    protected function parseModelFields(string $fieldsOption): array
    {
        $fields = [];
        foreach (explode(',', $fieldsOption) as $field) {
            [$name, $type] = array_pad(explode(':', $field), 2, 'string');
            $fields[] = [
                'name' => $name,
                'type' => $this->mapFieldType($type),
            ];
        }

        return $fields;
    }

    protected function mapFieldType(string $type): string
    {
        return match ($type) {
            'int', 'integer', 'bigint', 'smallint', 'tinyint', 'foreignId' => 'int',
            'bool', 'boolean' => 'bool',
            'float', 'double', 'decimal' => 'float',
            'json', 'array' => 'array',
            default => 'string',
        };
    }

    protected function buildDtoReplacements(array $fields): array
    {
        $constructorArgs = implode(",\n        ", array_map(
            fn ($f) => "public {$f['type']} \${$f['name']},",
            $fields
        ));

        $fromArrayArgs = implode(",\n            ", array_map(
            fn ($f) => "\$data['{$f['name']}']",
            $fields
        ));

        $toArrayBody = implode(",\n            ", array_map(
            fn ($f) => "'{$f['name']}' => \$this->{$f['name']}",
            $fields
        ));

        return [
            '{{constructor_args}}' => rtrim($constructorArgs, ','),
            '{{from_array_args}}' => $fromArrayArgs,
            '{{to_array_body}}' => $toArrayBody,
        ];
    }

    protected function buildValidationRules(array $fields): string
    {
        return implode("\n", array_map(
            function ($f) {
                $ruleType = match ($f['type']) {
                    'int' => 'integer', 'float' => 'numeric', 'bool' => 'boolean', 'array' => 'array', default => 'string'
                };
                return "    '{$f['name']}' => ['required', '{$ruleType}'],";
            },
            $fields
        ));
    }

    protected function cleanupModule(string $modulePath): void
    {
        if ($this->files->exists($modulePath)) {
            $this->files->deleteDirectory($modulePath);
            $this->info("Cleaned up incomplete module at {$modulePath}.");
        }
    }
}
