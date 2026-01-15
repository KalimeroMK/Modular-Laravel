<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\YamlModule;

use InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;

class YamlModuleParser
{
    public function __construct(protected string $file) {}

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
            foreach ($config['fields'] ?? [] as $fieldName => $fieldType) {
                if (is_string($fieldType)) {
                    $fields[] = "{$fieldName}:{$fieldType}";
                } elseif (is_array($fieldType)) {
                    // Handle array format if needed
                    foreach ($fieldType as $subFieldName => $subFieldType) {
                        $fields[] = "{$subFieldName}:{$subFieldType}";
                    }
                }
            }

            $relations = [];
            foreach ($config['relations'] ?? [] as $relationKey => $relationConfig) {
                // Handle string format like: 'comments: morphMany:Comment:commentable'
                if (is_string($relationConfig) && str_contains($relationConfig, ':')) {
                    $parts = explode(':', $relationConfig);
                    if (count($parts) >= 2) {
                        $relationType = mb_trim($parts[0]);
                        $relModel = mb_trim($parts[1] ?? '');
                        $morphName = mb_trim($parts[2] ?? $relationKey);
                        if (in_array($relationType, ['morphTo', 'morphMany', 'morphOne', 'morphToMany']) && $morphName) {
                            $relations[] = "{$relationKey}:{$relationType}:{$relModel}:{$morphName}";
                        } else {
                            $relations[] = "{$relationKey}:{$relationConfig}";
                        }
                    } else {
                        $relations[] = "{$relationKey}:{$relationConfig}";
                    }
                } elseif (is_string($relationConfig)) {
                    // Simple format: 'belongsTo' => 'Role' or 'commentable' => 'morphTo'
                    if ($relationConfig === 'morphTo') {
                        $relations[] = "{$relationKey}:morphTo";
                    } elseif ($relationKey === 'morphTo') {
                        $relations[] = "{$relationConfig}:morphTo";
                    } else {
                        $relations[] = "{$relationKey}:{$relationConfig}";
                    }
                } elseif (is_array($relationConfig)) {
                    if (isset($relationConfig['name'])) {
                        // Format: 'morphTo' => ['name' => 'commentable']
                        $relations[] = "{$relationConfig['name']}:{$relationKey}";
                    } elseif (is_array($relationConfig[0] ?? null)) {
                        // Format: 'morphMany' => [['model' => 'Comment', 'morph_name' => 'commentable']]
                        foreach ($relationConfig as $rel) {
                            $model = $rel['model'] ?? '';
                            $morphName = $rel['morph_name'] ?? $rel['name'] ?? '';
                            $relations[] = $morphName ? "{$model}:{$relationKey}:{$model}:{$morphName}" : "{$model}:{$relationKey}";
                        }
                    } elseif (is_string($relationConfig[0] ?? null)) {
                        // Format: 'belongsToMany' => ['Category', 'Tag']
                        foreach ($relationConfig as $model) {
                            $relations[] = "{$model}:{$relationKey}";
                        }
                    } else {
                        // Handle other array formats
                        foreach ($relationConfig as $subRelationName => $subRelationType) {
                            $relations[] = "{$subRelationName}:{$subRelationType}";
                        }
                    }
                }
            }

            $modules[$name] = [
                'fields' => $fields,
                'relations' => $relations,
                'raw_relations' => $config['relations'] ?? [],
                'exceptions' => $config['exceptions'] ?? false,
                'observers' => $config['observers'] ?? false,
                'policies' => $config['policies'] ?? false,
                'events' => $config['events'] ?? false,
                'enum' => $config['enum'] ?? false,
                'notifications' => $config['notifications'] ?? false,
            ];
        }

        return $modules;
    }
}
