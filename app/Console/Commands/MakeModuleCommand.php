<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

/**
 * Command to generate a new API module with all scaffolding for Laravel
 */
class MakeModuleCommand extends Command
{
    // Command signature and description
    protected $signature = 'make:module {name : The name of the module} {--migrations : Generate migration and model files with fields and relationships} {--model= : Model fields, e.g. name:string,coverImg:string,restaurant_id:foreignId} {--relations= : Eloquent relationships, e.g. user:belongsTo:User,orders:hasMany:Order} {--exceptions : Generate exception classes} {--observers : Generate observer stubs} {--policies : Generate policy stubs}';

    protected $description = 'Create a new API module with predefined structure and files';

    protected Filesystem $files;

    protected array $dtoFiles = [
        'Http/DTOs/{{module}}DTO.php' => 'stubs/module/Http/DTOs/DTO.stub',
    ];

    /**
     * Constructor: Injects the filesystem dependency.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Main execution method for the command.
     * Handles module creation, structure, and file generation.
     */
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
            $this->updateRepositoryServiceProvider($moduleName);
            // Remove Exceptions dir if --exceptions not passed
            if (! $this->option('exceptions')) {
                $exceptionsDir = "$modulePath/Exceptions";
                if ($this->files->exists($exceptionsDir)) {
                    $this->files->deleteDirectory($exceptionsDir);
                    $this->info('Exceptions directory removed (not requested).');
                }
            }
            $this->info("Module '{$moduleName}' is now up-to-date!");
            // After generating files, clear config and route cache and show route list
            \Artisan::call('optimize:clear');
        } catch (\Exception $e) {
            if (! $this->files->exists($modulePath)) {
                $this->cleanupModule($modulePath);
            }
            $this->error("Failed to update/create module: {$e->getMessage()}");

            return 1;
        }

        return 0;
    }

    /**
     * Creates the directory structure for the module.
     * Ensures all required folders exist.
     */
    protected function createModuleStructure(string $modulePath): void
    {
        $structure = [
            'Http/DTOs', 'Http/Actions', 'Http/Controllers', 'Http/Requests',
            'Http/Resources', 'Models', 'Repositories', 'Interfaces',
            'database/migrations', 'database/factories', 'routes',
            'Config', 'Exceptions', 'Helpers', 'Traits', 'Observers', 'Policies',
        ];

        foreach ($structure as $directory) {
            $path = "$modulePath/$directory";
            if (! $this->files->exists($path)) {
                $this->files->makeDirectory($path, 0755, true);
            }
        }
    }

    /**
     * Generates all required files for the module using stubs and dynamic replacements.
     * Handles models, migrations, factories, controllers, DTOs, actions, requests, exceptions, etc.
     */
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

        // --- DTO GENERATION PATCH ---
        // Always generate DTO with all fields, no types, no defaults, just one DTO per module
        $dtoReplacements = [
            '{{constructor_args}}' => implode(",\n        ", array_map(fn ($f) => '$'.$f['name'], $fields)),
            '{{from_array_args}}' => implode(",\n            ", array_map(fn ($f) => "\$data['{$f['name']}'] ?? null", $fields)),
            // Use double backslash and single quotes for literal output in stub
            '{{to_array_body}}' => implode(",\n            ", array_map(fn ($f) => "'{$f['name']}' => \$this->{$f['name']}", $fields)),
            '{{module}}' => $moduleName,
        ];
        // Patch only the DTO stub
        foreach ($this->dtoFiles as $target => $stubPath) {
            $outputPath = $modulePath.'/'.str_replace(['{{module}}'], [$moduleName], $target);
            $this->createFromStub($stubPath, $outputPath, $dtoReplacements);
        }
        // --- END DTO PATCH ---

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
            $outputPath = $modulePath.'/'.str_replace(array_keys($replacements), array_values($replacements), $target);
            $currentReplacements = $replacements;
            // Add factory_fields only for Factory.stub
            if ($stubPath === 'stubs/module/Factory.stub') {
                $currentReplacements['{{factory_fields}}'] = $this->buildFactoryFields($fields);
            }
            // Add migration_fields only for Migration.stub
            if ($stubPath === 'stubs/module/Migration.stub') {
                $currentReplacements['{{migration_fields}}'] = $this->buildMigrationFields($fields);
            }
            // Add fillable/casts/phpdoc_block only for Model.stub
            if ($stubPath === 'stubs/module/Model.stub') {
                $currentReplacements['{{fillable}}'] = $this->buildFillable($fields);
                $currentReplacements['{{casts}}'] = $this->buildCasts($fields);
                $currentReplacements['{{phpdoc_block}}'] = $this->buildPhpDocBlock($fields);
                $currentReplacements['{{relationships}}'] = $this->buildRelationships($this->option('relations'));
            }
            // Add moduleVar only for Controller.stub
            if ($stubPath === 'stubs/module/Http/Controllers/Controller.stub') {
                $currentReplacements['{{moduleVar}}'] = Str::camel($moduleName);
            }
            // Add resource_fields only for Resource.stub
            if ($stubPath === 'stubs/module/Http/Resource/Resource.stub') {
                $currentReplacements['{{resource_fields}}'] = $this->buildResourceFields($fields);
            }
            $this->createFromStub($stubPath, $outputPath, $currentReplacements);
        }

        // Generate Requests for Create and Update
        foreach (['Create', 'Update'] as $type) {
            $requestStub = base_path("stubs/module/Http/Requests/{$type}Request.stub");
            if ($this->files->exists($requestStub)) {
                $requestReplacements = [
                    '{{module}}' => $moduleName,
                    '{{validation_rules}}' => $this->buildValidationRules($fields),
                ];
                $filePath = "$modulePath/Http/Requests/{$type}{$moduleName}Request.php";
                $this->createFromStub($requestStub, $filePath, $requestReplacements);
            } else {
                $this->warn("Request stub not found: {$requestStub}");
            }
        }

        // Generate Exception classes only if --exceptions is passed
        if ($this->option('exceptions')) {
            $exceptionTypes = ['Store', 'Update', 'Delete', 'NotFound', 'Index'];
            $exceptionStub = base_path('stubs/module/Http/Exceptions/Exception.stub');
            foreach ($exceptionTypes as $type) {
                if ($this->files->exists($exceptionStub)) {
                    $exceptionClass = "{$moduleName}{$type}Exception";
                    $exceptionReplacements = [
                        '{{module}}' => $moduleName,
                        '{{exception}}' => $exceptionClass,
                    ];
                    $filePath = "$modulePath/Exceptions/{$exceptionClass}.php";
                    $this->createFromStub($exceptionStub, $filePath, $exceptionReplacements);
                } else {
                    $this->warn("Exception stub not found: {$exceptionStub}");
                }
            }
        }

        // Generate Actions for Create, Update, Delete, GetAll, GetById
        $actionTypes = ['Create', 'Update', 'Delete', 'GetAll', 'GetById'];
        foreach ($actionTypes as $type) {
            $actionStub = base_path("stubs/module/Http/Actions/{$type}Action.stub");
            if ($this->files->exists($actionStub)) {
                $actionReplacements = [
                    '{{module}}' => $moduleName,
                    '{{class}}' => $moduleName,
                    // Add more replacements here if your stub requires them
                ];
                $filePath = "$modulePath/Http/Actions/{$type}{$moduleName}Action.php";
                $this->createFromStub($actionStub, $filePath, $actionReplacements);
            } else {
                $this->warn("Action stub not found: {$actionStub}");
            }
        }

        // Generate Observer if --observers is passed
        if ($this->option('observers')) {
            $observerStub = base_path('stubs/module/Observers/ModelObserver.stub');
            if ($this->files->exists($observerStub)) {
                $observerClass = "{$moduleName}Observer";
                $observerReplacements = [
                    '{{module}}' => $moduleName,
                    '{{observer}}' => $observerClass,
                ];
                $filePath = "$modulePath/Observers/{$observerClass}.php";
                $this->createFromStub($observerStub, $filePath, $observerReplacements);
            }
        }

        // Generate Policy if --policies is passed
        if ($this->option('policies')) {
            $policyStub = base_path('stubs/module/Policies/ModelPolicy.stub');
            if ($this->files->exists($policyStub)) {
                $policyClass = "{$moduleName}Policy";
                $policyReplacements = [
                    '{{module}}' => $moduleName,
                    '{{policy}}' => $policyClass,
                ];
                $filePath = "$modulePath/Policies/{$policyClass}.php";
                $this->createFromStub($policyStub, $filePath, $policyReplacements);
            }
        }

        // --- CRUD FEATURE TEST GENERATION ---
        $testStub = base_path('stubs/tests/Feature/CrudTest.stub');
        $testTarget = base_path("tests/Feature/Modules/{$moduleName}CrudTest.php");
        if (file_exists($testStub)) {
            $testReplacements = [
                '{{module}}' => $moduleName,
                '{{module_lower}}' => strtolower($moduleName),
            ];

            // Find required fields or pick a string field for update
            $requiredFields = array_filter($fields, fn ($f) => ($f['required'] ?? false));
            $allStringFields = array_filter($fields, fn ($f) => $f['type'] === 'string');
            $updateField = count($allStringFields) > 0 ? $allStringFields[array_key_first($allStringFields)] : ($fields[0] ?? null);

            // Detect foreign keys for store/update data
            $foreignKeySetup = '';
            $storeData = [];
            $usedFields = count($requiredFields) ? $requiredFields : $fields;
            foreach ($usedFields as $f) {
                if (str_ends_with($f['name'], '_id') && in_array($f['type'], ['int', 'bigint', 'unsignedBigInteger'])) {
                    $relatedModel = ucfirst(str_replace('_id', '', $f['name']));
                    $foreignKeySetup .= "$relatedModel = \\App\\Models\\$relatedModel::factory()->create();\n";
                    $storeData[$f['name']] = "{$$relatedModel->id}";
                } else {
                    $storeData[$f['name']] = $f['type'] === 'bool' ? true : ($f['type'] === 'int' ? 123 : ($f['type'] === 'float' ? 1.23 : 'test'));
                }
            }
            // Convert $storeData to PHP code, handling foreign keys
            $storeDataString = var_export($storeData, true);
            // Replace quoted variable references with real PHP variables
            $storeDataString = preg_replace("/'\\$(\w+)->id'/", '".$\\$1->id."', $storeDataString);

            // Fill data for update (edit one string field or first field)
            $updateData = [];
            $updateSetup = '';
            if ($updateField) {
                if (str_ends_with($updateField['name'], '_id') && in_array($updateField['type'], ['int', 'bigint', 'unsignedBigInteger'])) {
                    $relatedModel = ucfirst(str_replace('_id', '', $updateField['name']));
                    $updateSetup .= "$relatedModel = \\App\\Models\\$relatedModel::factory()->create();\n";
                    $updateData[$updateField['name']] = "{$$relatedModel->id}";
                } else {
                    $updateData[$updateField['name']] = $updateField['type'] === 'string' ? 'updated' : ($updateField['type'] === 'bool' ? false : ($updateField['type'] === 'int' ? 456 : 4.56));
                }
            }
            $updateDataString = var_export($updateData, true);
            $updateDataString = preg_replace("/'\\$(\w+)->id'/", '".$\\$1->id."', $updateDataString);

            $testReplacements['{{store_data_setup}}'] = $foreignKeySetup;
            $testReplacements['{{store_data}}'] = $storeDataString;
            $testReplacements['{{update_data_setup}}'] = $updateSetup;
            $testReplacements['{{update_data}}'] = $updateDataString;

            $testContent = str_replace(array_keys($testReplacements), array_values($testReplacements), file_get_contents($testStub));
            if (! is_dir(dirname($testTarget))) {
                mkdir(dirname($testTarget), 0777, true);
            }
            file_put_contents($testTarget, $testContent);
            $this->info("CRUD feature test generated: tests/Feature/Modules/{$moduleName}CrudTest.php");
        }
        // --- END CRUD FEATURE TEST GENERATION ---
    }

    /**
     * Reads a stub file, replaces placeholders, and writes the output file.
     * Handles both absolute and relative stub paths.
     */
    protected function createFromStub(string $stubPath, string $outputPath, array $replacements): void
    {
        // If stubPath is absolute, use as is; otherwise, prepend base_path
        $isAbsolute = str_starts_with($stubPath, '/') || preg_match('/^[A-Za-z]:\\\\/', $stubPath);
        $fullStubPath = $isAbsolute ? $stubPath : base_path($stubPath);
        if (! $this->files->exists($fullStubPath)) {
            $this->warn("Stub not found: {$fullStubPath}");

            return;
        }
        $content = $this->files->get($fullStubPath);
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);

        $this->files->ensureDirectoryExists(dirname($outputPath));
        $this->files->put($outputPath, $content);
        $this->info("Created file: {$outputPath}");
    }

    /**
     * Parses the model fields option into an array of field definitions.
     * Example: title:string,price:float => [['name'=>'title','type'=>'string'], ...]
     * Supports both user_id:int and user_id:foreign:users:id
     */
    protected function parseModelFields(string $fieldsOption): array
    {
        $fields = [];
        foreach (explode(',', $fieldsOption) as $field) {
            $parts = explode(':', $field);
            $name = $parts[0];
            // If user_id:foreign:users:id
            if (isset($parts[1]) && $parts[1] === 'foreign') {
                $type = 'int';
                // Support migration field info for foreign keys
                $fields[] = [
                    'name' => $name,
                    'type' => 'foreign',
                    'references' => $parts[3] ?? 'id',
                    'on' => $parts[2] ?? (str_ends_with($name, '_id') ? rtrim($name, '_id').'s' : $name.'s'),
                ];
                continue;
            }
            $type = $this->mapFieldType($parts[1] ?? 'string');
            $fields[] = [
                'name' => $name,
                'type' => $type,
            ];
        }
        return $fields;
    }

    /**
     * Maps user-friendly field types to PHP/Laravel types.
     */
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

    /**
     * Builds the $fillable property for the model.
     */
    protected function buildFillable(array $fields): string
    {
        $names = array_map(fn ($f) => "'{$f['name']}'", $fields);

        return implode(', ', $names);
    }

    /**
     * Builds the $casts property for the model.
     */
    protected function buildCasts(array $fields): string
    {
        $lines = [];
        foreach ($fields as $field) {
            $type = match ($field['type']) {
                'int' => 'int',
                'float' => 'float',
                'bool' => 'bool',
                'array' => 'array',
                default => null,
            };
            if ($type) {
                $lines[] = "            '{$field['name']}' => '{$type}',";
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Builds the factory fields for the model's factory stub.
     * Maps types to suitable Faker data.
     */
    protected function buildFactoryFields(array $fields): string
    {
        $lines = [];
        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $field['type'];
            $faker = match ($type) {
                'string' => "\t'{$name}' => \$this->faker->sentence,",
                'float' => "\t'{$name}' => \$this->faker->randomFloat(2, 0, 1000),",
                'int' => "\t'{$name}' => \$this->faker->numberBetween(0, 1000),",
                'bool' => "\t'{$name}' => \$this->faker->boolean,",
                'array' => "\t'{$name}' => [],",
                default => "\t'{$name}' => null,",
            };
            $lines[] = $faker;
        }

        return implode("\n", $lines);
    }

    /**
     * Builds the migration fields for the migration stub.
     * Maps types to suitable Laravel migration columns.
     */
    protected function buildMigrationFields(array $fields): string
    {
        $lines = [];
        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $field['type'];
            if ($type === 'foreign') {
                $references = $field['references'];
                $on = $field['on'];
                $lines[] = "            \$table->foreignId('{$name}')->references('{$references}')->on('{$on}');";
            } else {
                $migrationType = match ($type) {
                    'string' => 'string',
                    'float' => 'float',
                    'int' => 'integer',
                    'bool' => 'boolean',
                    'array' => 'json',
                    default => 'string',
                };
                $lines[] = "            \$table->{$migrationType}('{$name}');";
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Builds the resource fields for the resource stub.
     * Used in toArray() for API resources.
     */
    protected function buildResourceFields(array $fields): string
    {
        $lines = [];
        foreach ($fields as $field) {
            $lines[] = "            '{$field['name']}' => \$this->{$field['name']},";
        }

        return implode("\n", $lines);
    }

    /**
     * Builds the PHPDoc block for the model.
     * Lists all properties and types.
     */
    protected function buildPhpDocBlock(array $fields): string
    {
        $lines = [];
        $lines[] = '/**';
        $lines[] = ' * @property int $id';
        foreach ($fields as $field) {
            $type = match ($field['type']) {
                'int' => 'int',
                'float' => 'float',
                'bool' => 'bool',
                'array' => 'array',
                default => 'string',
            };
            $lines[] = " * @property {$type} \${$field['name']}";
        }
        $lines[] = ' * @property \\Illuminate\\Support\\Carbon|null $created_at';
        $lines[] = ' * @property \\Illuminate\\Support\\Carbon|null $updated_at';
        $lines[] = ' */';

        return implode("\n", $lines);
    }

    /**
     * Builds the DTO replacements for Create/Update DTOs.
     * Handles constructor, fromRequest, and toArray code generation.
     */
    protected function buildDtoReplacements(array $fields): array
    {
        $constructorArgs = implode(",\n        ", array_map(
            function ($f) {
                $default = match ($f['type']) {
                    'int' => ' = null',
                    'float' => ' = 0.0',
                    'bool' => ' = false',
                    'array' => ' = []',
                    default => " = ''",
                };
                $nullable = $f['type'] === 'int' ? '?' : '';

                return "public {$nullable}{$f['type']} \\${$f['name']}{$default}";
            },
            $fields
        ));

        $fromArrayArgs = implode(",\n            ", array_map(
            function ($f) {
                $default = match ($f['type']) {
                    'int' => 'null',
                    'float' => '0.0',
                    'bool' => 'false',
                    'array' => '[]',
                    default => "''",
                };

                return "\$data['{$f['name']}'] ?? {$default}";
            },
            $fields
        ));

        $toArrayBody = implode(",\n            ", array_map(
            fn ($f) => "'{$f['name']}' => \$this->{$f['name']}",
            $fields
        ));

        return [
            '{{constructor_args}}' => $constructorArgs,
            '{{from_array_args}}' => $fromArrayArgs,
            '{{to_array_body}}' => $toArrayBody,
        ];
    }

    /**
     * Builds the validation rules for request stubs.
     * Maps types to suitable Laravel validation rules.
     */
    protected function buildValidationRules(array $fields): string
    {
        return implode("\n", array_map(
            function ($f) {
                $isForeignKey = str_ends_with($f['name'], '_id') && in_array($f['type'], ['int', 'bigint', 'unsignedBigInteger', 'foreign']);
                $ruleType = match ($f['type']) {
                    'int' => 'integer', 'float' => 'numeric', 'bool' => 'boolean', 'array' => 'array', 'foreign' => 'integer', default => 'string'
                };
                if ($isForeignKey) {
                    // Guess table name from field (user_id -> users)
                    $table = str_replace('_id', '', $f['name']);
                    $table = str_ends_with($table, 's') ? $table : $table . 's';
                    return "    '{$f['name']}' => ['required', 'integer', 'exists:{$table},id'], // foreign key, integer";
                }
                return "    '{$f['name']}' => ['required', '{$ruleType}'],";
            },
            array_filter($fields, fn($f) => $f['name'] !== 'id')
        ));
    }

    /**
     * Builds the Eloquent relationship methods for the model stub.
     * Maps relationships to suitable Eloquent methods.
     */
    protected function buildRelationships(?string $relationsOption): string
    {
        if (! $relationsOption) {
            return '';
        }
        $lines = [];
        $relations = array_filter(array_map('trim', explode(',', $relationsOption)));
        foreach ($relations as $relation) {
            // Format: relationName:type:RelatedModel
            [$name, $type, $related] = array_pad(explode(':', $relation), 3, null);
            if (! $name || ! $type || ! $related) {
                continue;
            }
            $method = "public function {$name}()\n    {\n        return \$this->{$type}({$related}::class);\n    }\n";
            $lines[] = $method;
        }

        return "\n".implode("\n", $lines);
    }

    /**
     * Cleans up module directory if generation fails.
     * Removes incomplete module folder.
     */
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
