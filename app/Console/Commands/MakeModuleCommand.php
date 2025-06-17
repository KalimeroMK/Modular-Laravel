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

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));

        $options = [
            'model' => $this->option('model') ?? '',
            'relations' => $this->option('relations') ?? '',
            'exceptions' => $this->option('exceptions'),
            'observers' => $this->option('observers'),
            'policies' => $this->option('policies'),
            'repositories' => [], // ensure safe default
        ];

        $fields = $this->parseFields($options['model']);

        try {
            $generator = app(ModuleGenerator::class);
            $generator->generate($name, $options);
            Artisan::call('optimize:clear');
            $this->info("✅ Module '{$name}' generated successfully.");
            return 0;
        } catch (Throwable $e) {
            $this->error("❌ Error generating module: {$e->getMessage()}");
            return 1;
        }
    }

    protected function parseFields(string $model): array
    {
        if (empty($model)) {
            return [];
        }

        return array_map(function ($field) {
            [$name, $type] = explode(':', $field);
            return ['name' => trim($name), 'type' => trim($type)];
        }, explode(',', $model));
    }
}
