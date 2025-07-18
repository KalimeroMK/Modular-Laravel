<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Modules\Core\Support\Generators\ModuleGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module
        {name : The name of the module}
        {--model= : Model fields, e.g. name:string,price:float}
        {--relations= : Eloquent relationships, e.g. user:belongsTo:User}
        {--exceptions : Generate exception classes}
        {--observers : Generate observer stubs}
        {--policies : Generate policy stubs}';

    protected $description = 'Create a new API module with predefined structure and files';

    /**
     * Handle the command execution
     * 
     * @return int
     */
    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));

        /** @var array{model: string, relations: string, exceptions: bool, observers: bool, policies: bool, repositories: array<mixed>, table?: string, relationships?: string} $options */
        $options = [
            'model' => $this->option('model') ?? '',
            'relations' => $this->option('relations') ?? '',
            'exceptions' => $this->option('exceptions'),
            'observers' => $this->option('observers'),
            'policies' => $this->option('policies'),
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

    /**
     * Parse model fields from string format to structured array
     *
     * @param string $model
     * @return array<int, array{name: string, type: string}>
     */
    protected function parseFields(string $model): array
    {
        if (empty($model)) {
            return [];
        }

        /** @var array<int, array{name: string, type: string}> $result */
        $result = array_map(function ($field) {
            [$name, $type] = explode(':', $field);
            return ['name' => trim($name), 'type' => trim($type)];
        }, explode(',', $model));

        return $result;
    }

    protected function buildRelationships(string $relations): string
    {
        if (empty($relations)) {
            return '';
        }

        $lines = [];
        foreach (explode(',', $relations) as $rel) {
            $parts = explode(':', $rel);
            if (count($parts) < 2) continue;
            $relName = trim($parts[0]);
            $relType = trim($parts[1]);
            $relModel = $parts[2] ?? ucfirst($relName);
            
            // Handle polymorphic relationships
            if (in_array($relType, ['morphTo', 'morphMany', 'morphOne', 'morphToMany'])) {
                $lines[] = $this->buildPolymorphicRelationship($relName, $relType, $relModel, $parts);
            } else {
                $lines[] = "    public function {$relName}()\n    {\n        return \$this->{$relType}({$relModel}::class);\n    }\n";
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Build polymorphic relationship method
     *
     * @param string $relName
     * @param string $relType
     * @param string $relModel
     * @param array<int, string> $parts
     * @return string
     */
    protected function buildPolymorphicRelationship(string $relName, string $relType, string $relModel, array $parts): string
    {
        switch ($relType) {
            case 'morphTo':
                return "    public function {$relName}()\n    {\n        return \$this->morphTo();\n    }\n";
            
            case 'morphMany':
                $morphName = $parts[3] ?? $relName;
                return "    public function {$relName}()\n    {\n        return \$this->morphMany({$relModel}::class, '{$morphName}');\n    }\n";
            
            case 'morphOne':
                $morphName = $parts[3] ?? $relName;
                return "    public function {$relName}()\n    {\n        return \$this->morphOne({$relModel}::class, '{$morphName}');\n    }\n";
            
            case 'morphToMany':
                $morphName = $parts[3] ?? $relName;
                return "    public function {$relName}()\n    {\n        return \$this->morphToMany({$relModel}::class, '{$morphName}');\n    }\n";
            
            default:
                // Fallback to standard relationship
                return "    public function {$relName}()\n    {\n        return \$this->{$relType}({$relModel}::class);\n    }\n";
        }
    }
}
