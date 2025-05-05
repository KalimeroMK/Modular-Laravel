<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name : The name of the module} {--api : Generate an API controller and routes} {--migrations : Generate migration and model files with fields and relationships} {--model= : Model fields, e.g. name:string,coverImg:string,restaurant_id:foreignId} {--relationships= : Model relationships, e.g. restaurant:belongsTo,pictures:hasMany}';

    protected $description = 'Create a new module with predefined structure and files';

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
        $isApi = $this->option('api');

        // Instead of failing if the module folder exists, just warn and continue
        if ($this->files->exists($modulePath)) {
            $this->warn("Module '{$moduleName}' already exists. We'll only create missing files.");
        } else {
            $this->files->makeDirectory($modulePath, 0755, true);
        }

        try {
            // Create the sub-folders (will skip any that already exist)
            $this->createModuleStructure($modulePath, $isApi);

            // Generate missing files
            $this->generateFiles($modulePath, $moduleName, $isApi);

            // Update the RepositoryServiceProvider (if the interface binding isn't there yet)
            $this->updateRepositoryServiceProvider($moduleName);

            $this->info("Module '{$moduleName}' is now up-to-date!");
        } catch (\Exception $e) {
            // If it's a brand-new module and something fails, remove partial directory
            // only if the directory truly didn't exist before.
            if (! $this->files->exists($modulePath)) {
                $this->cleanupModule($modulePath);
            }
            $this->error("Failed to update/create module: {$e->getMessage()}");

            return 1;
        }

        return 0;
    }

    protected function createModuleStructure(string $modulePath, bool $isApi): void
    {
        // Shared structure (directories that are relevant for both web and API).
        $structure = [
            'Config',
            'Http/Requests',
            'Helpers',
            'Interfaces',
            'Models',
            'Repositories',
            'routes',
            'Traits',
            'database/migrations',
            'database/factories',
            'Http/DTOs',
            'Http/Actions',
        ];

        // If it's an API module, add additional subfolders relevant to API usage
        // If it's an API module, add additional subfolders relevant to API usage
        if ($isApi) {
            $structure = array_merge($structure, [
                'Http/Controllers/Api',
                'Http/Resources',
                'Exceptions',
            ]);
        } else {
            // Add only for Web
            $structure = array_merge($structure, [
                'Resources/lang',
                'Resources/views/layouts',
                'Resources/views',
            ]);
        }

        // Create each directory if it doesn't already exist
        foreach ($structure as $directory) {
            $path = "$modulePath/$directory";
            if (! $this->files->exists($path)) {
                $this->files->makeDirectory($path, 0755, true);
            }
        }
    }

    protected function generateFiles(string $modulePath, string $moduleName, bool $isApi): void
    {
        $tableName = Str::plural(Str::snake($moduleName));

        // Ensure $table is defined for use in stubs
        $table = Str::snake(Str::plural($moduleName));

        // 1. Start with all stubs (both web and API).
        // 2. Then remove the web stubs if it's an API-only run,
        //    or remove the API stubs if it's a web-only run.
        $allStubs = [
            // -- Shared stubs
            'Interfaces/{{module}}Interface.php' => base_path('stubs/module/Interface.stub'),
            'Repositories/{{module}}Repository.php' => base_path('stubs/module/Repository.stub'),
            'Models/{{module}}.php' => base_path('stubs/module/Model.stub'),
            'database/factories/{{module}}Factory.php' => base_path('stubs/module/Factory.stub'),
            'Http/DTOs/{{module}}DTO.php' => base_path('stubs/module/Http/DTOs/DTO.stub'),
            // CRUD actions
            'Http/Actions/Create{{module}}Action.php' => base_path('stubs/module/Http/Actions/CreateAction.stub'),
            'Http/Actions/Update{{module}}Action.php' => base_path('stubs/module/Http/Actions/UpdateAction.stub'),
            'Http/Actions/Delete{{module}}Action.php' => base_path('stubs/module/Http/Actions/DeleteAction.stub'),
            'Http/Actions/Show{{module}}Action.php' => base_path('stubs/module/Http/Actions/ShowAction.stub'),
        ];

        // API stubs (only needed if --api is set)
        $apiStubs = [
            'Http/Controllers/Api/{{module}}Controller.php' => base_path('stubs/module/Http/Controllers/ApiController.stub'),
            'routes/api.php' => base_path('stubs/module/routes/api.stub'),
            'Http/Resources/{{module}}Resource.php' => base_path('stubs/module/Http/Resource/Resource.stub'),
        ];

        // Exception stubs for API
        $exceptionStubs = [
            'Exceptions/{{module}}DestroyException.php' => base_path('stubs/module/Http/Exceptions/DestroyException.stub'),
            'Exceptions/{{module}}IndexException.php' => base_path('stubs/module/Http/Exceptions/IndexException.stub'),
            'Exceptions/{{module}}NotFoundException.php' => base_path('stubs/module/Http/Exceptions/NotFoundException.stub'),
            'Exceptions/{{module}}SearchException.php' => base_path('stubs/module/Http/Exceptions/SearchException.stub'),
            'Exceptions/{{module}}StoreException.php' => base_path('stubs/module/Http/Exceptions/StoreException.stub'),
            'Exceptions/{{module}}UpdateException.php' => base_path('stubs/module/Http/Exceptions/UpdateException.stub'),
        ];

        // Request stubs (common to both but we list them here;
        // they're not strictly 'web' or 'api' only,
        // though you could treat them as needed).
        $requestStubs = [
            'Http/Requests/Create{{module}}Request.php' => base_path('stubs/module/Http/Request/CreateRequest.stub'),
            'Http/Requests/Delete{{module}}Request.php' => base_path('stubs/module/Http/Request/DeleteRequest.stub'),
            'Http/Requests/Search{{module}}Request.php' => base_path('stubs/module/Http/Request/SearchRequest.stub'),
            'Http/Requests/Show{{module}}Request.php' => base_path('stubs/module/Http/Request/ShowRequest.stub'),
            'Http/Requests/Update{{module}}Request.php' => base_path('stubs/module/Http/Request/UpdateRequest.stub'),
        ];

        // Merge everything into $allStubs
        // Then if it's API, add $apiStubs + $exceptionStubs.
        // If not API, skip them.
        // In any case, add $requestStubs (assuming both web and API might use requests).
        if ($isApi) {
            $allStubs = array_merge($allStubs, $apiStubs, $exceptionStubs);
            // Optionally, remove Web stubs if you never want them in an API-only run:
            unset(
                $allStubs['Http/Controllers/{{module}}Controller.php'],
                $allStubs['routes/web.php'],
                $allStubs['Resources/views/index.blade.php'],
                $allStubs['Resources/views/create.blade.php'],
                $allStubs['Resources/views/show.blade.php'],
                $allStubs['Resources/views/layouts/master.blade.php']
            );
        } else {
            // If it's NOT API, remove the API stubs
            unset(
                $apiStubs['Http/Controllers/Api/{{module}}Controller.php'],
                $apiStubs['routes/api.php'],
                $apiStubs['Http/Resources/{{module}}Resource.php']
            );
            unset(
                $exceptionStubs['Exceptions/{{module}}DestroyException.php'],
                $exceptionStubs['Exceptions/{{module}}IndexException.php'],
                $exceptionStubs['Exceptions/{{module}}NotFoundException.php'],
                $exceptionStubs['Exceptions/{{module}}SearchException.php'],
                $exceptionStubs['Exceptions/{{module}}StoreException.php'],
                $exceptionStubs['Exceptions/{{module}}UpdateException.php']
            );
        }

        // Add Request stubs to the final array
        $allStubs = array_merge($allStubs, $requestStubs);

        // Parse model fields and relationships for validation and resource fields
        $modelFields = $this->option('model') ? explode(',', $this->option('model')) : [];
        $relationships = $this->option('relationships') ? explode(',', $this->option('relationships')) : [];

        $validationRules = [];
        $resourceFields = [];
        $factoryFields = [];

        // Build a set of relationship names for easy lookup
        $relationshipNames = [];
        foreach ($relationships as $relationDef) {
            if (! $relationDef) {
                continue;
            }
            [$rel, $relType] = array_pad(explode(':', $relationDef), 2, '');
            $relationshipNames[] = $rel;
        }

        if (! empty($modelFields) && $modelFields[0] !== '') {
            foreach ($modelFields as $fieldDef) {
                if (! $fieldDef) {
                    continue;
                }
                [$field, $type] = array_map('trim', explode(':', $fieldDef));
                // If this is a foreignId and a matching relationship exists (name = field minus _id), skip adding $this->$field to resourceFields
                if ($type === 'foreignId') {
                    $related = str_replace('_id', '', $field);
                    if (in_array($related, $relationshipNames)) {
                        // Only skip resource field, still add factory and validation
                        $factoryFields[] = "            '$field' => fn() => \\App\\Modules\\".Str::plural($related).'::factory(),';
                        $validationRules[] = "    '$field' => ['required', 'exists:".Str::plural($related).",id'],";

                        continue; // SKIP resource field!
                    }
                }
                $resourceFields[] = "    '{$field}' => \$this->{$field},";
                $ruleType = $type === 'int' || $type === 'integer' ? 'integer' : ($type === 'bool' ? 'boolean' : 'string');
                $validationRules[] = "    '$field' => ['required', '$ruleType'],";
                // Faker type mapping
                $faker = match ($type) {
                    'string' => '$this->faker->word',
                    'text' => '$this->faker->sentence',
                    'email' => '$this->faker->safeEmail',
                    'int', 'integer' => '$this->faker->randomNumber()',
                    'bool', 'boolean' => '$this->faker->boolean',
                    'date' => '$this->faker->date()',
                    'datetime' => '$this->faker->dateTime()',
                    default => '$this->faker->word'
                };
                $factoryFields[] = "            '$field' => $faker,";
            }
        }
        // Add relationship resource fields
        foreach ($relationships as $rel) {
            [$relName, $relType] = array_map('trim', explode(':', $rel));
            $relStudly = Str::studly($rel);
            if ($relType === 'belongsTo') {
                $resourceFields[] = "    '$rel' => new {$relStudly}Resource(\$this->whenLoaded('$rel')),";
            } elseif ($relType === 'hasMany') {
                $resourceFields[] = "    '$rel' => {$relStudly}Resource::collection(\$this->whenLoaded('$rel')),";
            }
        }

        // Now generate all stubs in the final array
        foreach ($allStubs as $file => $stubPath) {
            if (! $this->files->exists($stubPath)) {
                $this->error("Stub file not found: {$stubPath}");

                continue;
            }

            $stubContent = $this->files->get($stubPath);

            // Action class placeholder replacements
            if (str_contains($file, 'Actions/')) {
                $actionNamespace = "App\\Modules\\{$moduleName}\\Http\\Actions";
                $modelNamespace = "App\\Modules\\{$moduleName}\\Models\\{$moduleName}";
                // e.g. UpdateTestAction
                $className = str_replace(['Http/Actions/', '.php'], '', $file);
                // e.g. Test
                $modelName = $moduleName;
                // e.g. test
                $modelVariable = Str::camel($moduleName);
                $stubContent = str_replace([
                    '{{namespace}}',
                    '{{modelNamespace}}',
                    '{{className}}',
                    '{{modelName}}',
                    '{{modelVariable}}',
                ], [
                    $actionNamespace,
                    $modelNamespace,
                    $className,
                    $modelName,
                    $modelVariable,
                ], $stubContent);
            }

            // DTO special handling
            if (str_contains($file, 'DTO.php') && $this->option('model')) {
                $fields = $this->parseModelFields($this->option('model'));
                $dtoReplacements = $this->buildDtoReplacements($fields);
                $stubContent = str_replace(array_keys($dtoReplacements), array_values($dtoReplacements), $stubContent);
            }

            $content = str_replace(
                ['{{module}}', '{{module_lower}}', '{{table}}', '{{timestamp}}'],
                [
                    $moduleName,
                    Str::lower($moduleName),
                    $tableName,
                    now()->format('Y_m_d_His'),
                ],
                $stubContent
            );

            // Inject validation rules and resource fields if placeholders exist
            if (str_contains($content, '{{validation_rules}}')) {
                $content = str_replace('{{validation_rules}}', implode("\n", $validationRules), $content);
            }
            if (str_contains($content, '{{resource_fields}}')) {
                $content = str_replace('{{resource_fields}}', implode("\n", $resourceFields), $content);
            }
            // Inject factory fields if placeholder exists
            if (str_contains($content, '{{factory_fields}}')) {
                $content = str_replace('{{factory_fields}}', implode("\n", $factoryFields), $content);
            }

            $filePath = "$modulePath/".str_replace(
                ['{{module}}'],
                [$moduleName],
                $file
            );

            // If file already exists, skip it
            if ($this->files->exists($filePath)) {
                $this->info("File already exists, skipping: {$filePath}");

                continue;
            }

            // Otherwise, create it
            $this->files->ensureDirectoryExists(dirname($filePath));
            $this->files->put($filePath, $content);
            $this->info("Created file: {$filePath}");
        }

        // Handle migrations if the flag is set
        if ($this->option('migrations')) {
            // Parse model fields
            $fields = [];
            if ($this->option('model')) {
                $fields = array_map('trim', explode(',', $this->option('model')));
            }
            // Parse relationships
            $relationships = [];
            if ($this->option('relationships')) {
                $relationships = array_map('trim', explode(',', $this->option('relationships')));
            }
            // Generate migration file
            $migrationStub = base_path('stubs/module/Migration.stub');
            if ($this->files->exists($migrationStub)) {
                $migrationContent = $this->files->get($migrationStub);
                // Insert fields into migration stub
                $schemaFields = '';
                foreach ($modelFields as $fieldDef) {
                    [$name, $type] = array_map('trim', explode(':', $fieldDef));
                    if ($type === 'foreignId') {
                        $schemaFields .= "            \$table->foreignId('$name')->constrained();\n";
                    } else {
                        $schemaFields .= "            \$table->$type('$name');\n";
                    }
                }
                // Replace {{table}} with actual table name
                $migrationContent = str_replace('{{table}}', $tableName, $migrationContent);
                // Insert fields
                $migrationContent = str_replace('// FIELDS', rtrim($schemaFields), $migrationContent);
                $migrationFileName = date('Y_m_d_His')."_create_{$tableName}_table.php";
                $migrationFilePath = "$modulePath/database/migrations/{$migrationFileName}";
                if (! $this->files->exists($migrationFilePath)) {
                    $this->files->put($migrationFilePath, $migrationContent);
                    $this->info("Created migration: {$migrationFilePath}");
                }
            }
            // Update model stub to include fillable fields and relationships
            $modelStub = base_path('stubs/module/Model.stub');
            if ($this->files->exists($modelStub)) {
                $modelContent = $this->files->get($modelStub);
                // Replace placeholders with actual values
                $modelContent = str_replace(['{{module}}', '{{table}}'], [$moduleName, $tableName], $modelContent);
                // Fillable
                $fillable = array_map(function ($f) {
                    return "'".explode(':', $f)[0]."'";
                }, $modelFields);
                $modelContent = str_replace('// Define fillable fields here', implode(",\n        ", $fillable), $modelContent);
                // Relationships
                $relationshipMethods = '';
                foreach ($relationships as $rel) {
                    [$relName, $relType] = array_map('trim', explode(':', $rel));
                    $method = "    public function $relName()\n    {\n        return \$this->$relType(".ucfirst(Str::singular($relName))."::class);\n    }\n";
                    $relationshipMethods .= "\n$method";
                }
                // Insert relationship methods at the placeholder
                $modelContent = str_replace('// RELATIONSHIPS', trim($relationshipMethods), $modelContent);
                $modelFilePath = "$modulePath/Models/{$moduleName}.php";
                if ($this->files->exists($modelFilePath)) {
                    $this->files->put($modelFilePath, $modelContent);
                    $this->info("Updated model: {$modelFilePath}");
                }
            }
        }
    }

    // --- DTO Generation Helpers ---
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
            fn ($f) => "{$f['type']} \${$f['name']}",
            $fields
        ));
        $fromArrayArgs = implode(",\n            ", array_map(
            fn ($f) => "\$data['{$f['name']}']",
            $fields
        ));
        // Property declarations
        $propertyDeclarations = implode("\n    ", array_map(
            fn ($f) => "public {$f['type']} \${$f['name']};",
            $fields
        ));
        // Constructor body
        $constructorBody = implode("\n        ", array_map(
            fn ($f) => "\$this->{$f['name']} = \${$f['name']};",
            $fields
        ));
        // toArray body
        $toArrayBody = implode(",\n            ", array_map(
            fn ($f) => "'{$f['name']}' => \$this->{$f['name']}",
            $fields
        ));

        return [
            '{{fields}}' => $propertyDeclarations,
            '{{constructor_args}}' => $constructorArgs,
            '{{constructor_body}}' => $constructorBody,
            '{{from_array_args}}' => $fromArrayArgs,
            '{{to_array_body}}' => $toArrayBody,
        ];
    }

    protected function cleanupModule(string $modulePath): void
    {
        if ($this->files->exists($modulePath)) {
            $this->files->deleteDirectory($modulePath);
            $this->info("Cleaned up incomplete module at {$modulePath}.");
        }
    }

    protected function updateRepositoryServiceProvider(string $moduleName): void
    {
        $providerPath = app_path('Providers/RepositoryServiceProvider.php');
        $interface = "App\\Modules\\{$moduleName}\\Interfaces\\{$moduleName}Interface";
        $repository = "App\\Modules\\{$moduleName}\\Repositories\\{$moduleName}Repository";

        if (! $this->files->exists($providerPath)) {
            $this->error("RepositoryServiceProvider.php not found at {$providerPath}");

            return;
        }

        $content = $this->files->get($providerPath);
        $pattern = '/protected\s+array\s+\$repositories\s*=\s*\[(.*?)\];/s';

        if (preg_match($pattern, $content, $matches)) {
            $existingEntries = trim($matches[1]);
            $newEntry = "        \\{$interface}::class => \\{$repository}::class,";

            // If it’s already there, skip
            if (Str::contains($existingEntries, $newEntry)) {
                $this->info("Entry for {$interface} already exists in RepositoryServiceProvider.php");

                return;
            }

            // Otherwise, add it
            $updatedEntries = $existingEntries ? "{$existingEntries}\n{$newEntry}" : $newEntry;
            $replacement = "protected array \$repositories = [\n{$updatedEntries}\n];";
            $content = preg_replace($pattern, $replacement, $content);

            $this->files->put($providerPath, $content);
            $this->info("Successfully updated RepositoryServiceProvider with {$interface} binding.");
        } else {
            $this->error('Could not locate $repositories array in RepositoryServiceProvider.php');
        }
    }
}
