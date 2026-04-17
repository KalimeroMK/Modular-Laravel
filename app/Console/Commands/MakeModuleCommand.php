<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Modules\Core\Support\Generators\ModuleGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Throwable;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module
        {name? : The name of the module}
        {--model= : Model fields, e.g. name:string,price:float}
        {--relations= : Eloquent relationships, e.g. user:belongsTo:User}
        {--exceptions : Generate exception classes}
        {--observers : Generate observer stubs}
        {--policies : Generate policy stubs}
        {--events : Generate event and listener classes}
        {--enum : Generate enum class}
        {--notifications : Generate notification classes}';

    protected $description = 'Create a new API module with predefined structure and files';

    


    public function handle(): int
    {
        $nameArg = $this->argument('name');
        $hasOptions = $this->option('model') || $this->option('relations') || $this->option('exceptions')
            || $this->option('observers') || $this->option('policies') || $this->option('events')
            || $this->option('enum') || $this->option('notifications');

        
        if ($nameArg || $hasOptions) {
            return $this->runNonInteractive($nameArg);
        }

        
        return $this->runInteractive();
    }

    


    protected function runInteractive(): int
    {
        info('🚀 Welcome to the Module Generator Wizard!');
        info('This wizard will guide you through creating a new API module.');

        /** @phpstan-ignore-next-line */
        $responses = form()
            ->text(
                label: 'What is the name of the module?',
                placeholder: 'e.g., Product, Order, Comment',
                required: true,
                name: 'name'
            )
            ->textarea(
                label: 'Enter model fields (format: name:type, one per line)',
                placeholder: "name:string\nprice:float\nstock:int\nis_active:bool",
                hint: 'Format: field_name:type (e.g., name:string, price:float)',
                name: 'model'
            )
            ->textarea(
                label: 'Enter relationships (format: relation:type:Model, one per line)',
                placeholder: "owner:belongsTo:User\nreviews:hasMany:Review",
                hint: 'Format: relation_name:relationship_type:ModelName (e.g., user:belongsTo:User)',
                name: 'relations'
            )
            ->add(fn ($responses) => multiselect(
                label: 'Select additional features to generate',
                options: [
                    'exceptions' => 'Exception classes',
                    'observers' => 'Observer stubs',
                    'policies' => 'Policy stubs',
                    'events' => 'Event and Listener classes',
                    'enum' => 'Enum class',
                    'notifications' => 'Notification classes',
                ],
                default: [],
                name: 'features'
            ))
            ->submit();

        $name = Str::studly($responses['name']);
         
        $modelFields = $this->parseTextareaFields($responses['model'] ?? '');
        $relations = $this->parseTextareaFields($responses['relations'] ?? '');
        $features = $responses['features'] ?? [];

         
        $options = [
            'model' => $this->fieldsToString($modelFields),
            'relations' => implode(',', $relations),
            'exceptions' => in_array('exceptions', $features, true),
            'observers' => in_array('observers', $features, true),
            'policies' => in_array('policies', $features, true),
            'events' => in_array('events', $features, true),
            'enum' => in_array('enum', $features, true),
            'notifications' => in_array('notifications', $features, true),
            'repositories' => [],
        ];

        $options['table'] = Str::plural(Str::snake($name));
        $options['relationships'] = $this->buildRelationships($options['relations']);

         
        $fields = $modelFields;

        try {
            $generator = app(ModuleGenerator::class);
            $generator->generate($name, $fields, $options);
            Artisan::call('optimize:clear');
            /** @phpstan-ignore-next-line */
            outro("✅ Module '{$name}' generated successfully!");

            return 0;
        } catch (Throwable $e) {
            $this->error("❌ Error generating module: {$e->getMessage()}");

            return 1;
        }
    }

    


    protected function runNonInteractive(?string $nameArg): int
    {
        $name = Str::studly($nameArg ?? 'Module');

         
        $options = [
            'model' => $this->option('model') ?? '',
            'relations' => $this->option('relations') ?? '',
            'exceptions' => $this->option('exceptions'),
            'observers' => $this->option('observers'),
            'policies' => $this->option('policies'),
            'events' => $this->option('events'),
            'enum' => $this->option('enum'),
            'notifications' => $this->option('notifications'),
            'repositories' => [],
        ];

        $options['table'] = Str::plural(Str::snake($name));
        $options['relationships'] = $this->buildRelationships($options['relations']);

        $fields = $this->parseFields($options['model']);

        try {
            $generator = app(ModuleGenerator::class);
            $generator->generate($name, $fields, $options);
            Artisan::call('optimize:clear');
            $this->info("✅ Module '{$name}' generated successfully.");

            return 0;
        } catch (Throwable $e) {
            $this->error("❌ Error generating module: {$e->getMessage()}");

            return 1;
        }
    }

    




    protected function parseTextareaFields(string $input): array
    {
        if ($input === '' || $input === '0') {
            return [];
        }

        $lines = explode("\n", $input);
        $fields = [];

        foreach ($lines as $line) {
            $line = mb_trim($line);
            if ($line !== '' && $line !== '0' && str_contains($line, ':')) {
                [$name, $type] = explode(':', $line, 2);
                $fields[] = ['name' => mb_trim($name), 'type' => mb_trim($type)];
            }
        }

        return $fields;
    }

    




    protected function fieldsToString(array $fields): string
    {
        return implode(',', array_map(fn ($field) => "{$field['name']}:{$field['type']}", $fields));
    }

    




    protected function parseFields(string $model): array
    {
        if ($model === '' || $model === '0') {
            return [];
        }

         
        $result = array_map(function ($field) {
            [$name, $type] = explode(':', $field);

            return ['name' => mb_trim($name), 'type' => mb_trim($type)];
        }, explode(',', $model));

        return $result;
    }

    protected function buildRelationships(string $relations): string
    {
        if ($relations === '' || $relations === '0') {
            return '';
        }

        $lines = [];
        $imports = [];
        foreach (explode(',', $relations) as $rel) {
            $parts = explode(':', $rel);
            if (count($parts) < 2) {
                continue;
            }
            $relName = mb_trim($parts[0]);
            $relType = mb_trim($parts[1]);
            $relModel = $parts[2] ?? ucfirst($relName);

            
            $imports[] = "use App\\Modules\\{$relModel}\\Infrastructure\\Models\\{$relModel};";

            
            if (in_array($relType, ['morphTo', 'morphMany', 'morphOne', 'morphToMany'])) {
                $lines[] = $this->buildPolymorphicRelationship($relName, $relType, $relModel, $parts);
            } else {
                $lines[] = "    public function {$relName}()\n    {\n        return \$this->{$relType}({$relModel}::class);\n    }";
            }
        }

        
        $imports = array_unique($imports);

        return implode("\n", $imports)."\n\n".implode("\n", $lines);
    }

    




    protected function buildPolymorphicRelationship(string $relName, string $relType, string $relModel, array $parts): string
    {
        switch ($relType) {
            case 'morphTo':
                return "    public function {$relName}()\n    {\n        return \$this->morphTo();\n    }";

            case 'morphMany':
                $morphName = $parts[3] ?? $relName;

                return "    public function {$relName}()\n    {\n        return \$this->morphMany({$relModel}::class, '{$morphName}');\n    }";

            case 'morphOne':
                $morphName = $parts[3] ?? $relName;

                return "    public function {$relName}()\n    {\n        return \$this->morphOne({$relModel}::class, '{$morphName}');\n    }";

            case 'morphToMany':
                $morphName = $parts[3] ?? $relName;

                return "    public function {$relName}()\n    {\n        return \$this->morphToMany({$relModel}::class, '{$morphName}');\n    }";

            default:
                
                return "    public function {$relName}()\n    {\n        return \$this->{$relType}({$relModel}::class);\n    }";
        }
    }
}
