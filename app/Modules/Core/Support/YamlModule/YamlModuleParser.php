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
            foreach ($config['relations'] ?? [] as $relationType => $relationConfig) {
                if (is_string($relationConfig)) {
                    // Simple format: 'belongsTo' => 'Role'
                    $relations[] = "{$relationType}:{$relationConfig}";
                } elseif (is_array($relationConfig)) {
                    if (isset($relationConfig['name'])) {
                        // Format: 'morphTo' => ['name' => 'commentable']
                        $relations[] = "{$relationConfig['name']}:{$relationType}";
                    } elseif (is_array($relationConfig[0] ?? null)) {
                        // Format: 'morphMany' => [['model' => 'Comment', 'morph_name' => 'commentable']]
                        foreach ($relationConfig as $rel) {
                            $model = $rel['model'] ?? '';
                            $morphName = $rel['morph_name'] ?? $rel['name'] ?? '';
                            if ($morphName) {
                                $relations[] = "{$model}:{$relationType}:{$model}:{$morphName}";
                            } else {
                                $relations[] = "{$model}:{$relationType}";
                            }
                        }
                    } elseif (is_string($relationConfig[0] ?? null)) {
                        // Format: 'belongsToMany' => ['Category', 'Tag']
                        foreach ($relationConfig as $model) {
                            $relations[] = "{$model}:{$relationType}";
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
            ];
        }

        return $modules;
    }
}
