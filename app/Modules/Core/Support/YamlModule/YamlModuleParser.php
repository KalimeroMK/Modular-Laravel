<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\YamlModule;

use InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;

class YamlModuleParser
{
    protected string $file;

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function parse(): array
    {
        $data = Yaml::parseFile($this->file);

        if (! isset($data['modules'])) {
            throw new InvalidArgumentException("YAML must contain 'modules' key.");
        }

        $modules = [];

        foreach ($data['modules'] as $name => $config) {
            $fields = [];
            foreach ($config['fields'] ?? [] as $field => $type) {
                $fields[] = "{$field}:{$type}";
            }

            $relations = [];
            foreach ($config['relations'] ?? [] as $type => $related) {
                if (is_array($related)) {
                    foreach ($related as $model) {
                        if (is_array($model)) {
                            // Handle polymorphic relations with structure like ['model' => 'Comment', 'morph_name' => 'commentable']
                            $modelName = $model['model'] ?? '';
                            $morphName = $model['morph_name'] ?? $model['name'] ?? '';
                            if ($modelName && $morphName) {
                                $relations[] = "{$modelName}:{$type}:{$modelName}:{$morphName}";
                            } elseif ($modelName) {
                                $relations[] = "{$modelName}:{$type}";
                            }
                        } else {
                            // Simple model name as string
                            $relations[] = "{$model}:{$type}";
                        }
                    }
                } elseif (is_string($related)) {
                    $relations[] = "{$type}:{$related}";
                }
            }

            $modules[$name] = [
                'fields' => $fields,
                'relations' => $relations,
                'raw_relations' => $config['relations'] ?? [],
                'exceptions' => $config['exceptions'] ?? false,
                'observers' => $config['observers'] ?? false,
                'policies' => $config['policies'] ?? false,
            ];
        }

        return $modules;
    }
}
