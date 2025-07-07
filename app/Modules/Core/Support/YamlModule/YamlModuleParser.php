<?php

namespace App\Modules\Core\Support\YamlModule;

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

        if (!isset($data['modules'])) {
            throw new \InvalidArgumentException("YAML must contain 'modules' key.");
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
                        $relations[] = "{$model}:{$type}";
                    }
                } elseif (is_string($related)) {
                    $relations[] = "{$type}:{$related}";
                }
            }

            $modules[$name] = [
                'fields' => $fields,
                'relations' => $relations,
                'raw_relations' => $config['relations'] ?? [], // ✅ ОВА ДОДАЈ ГО
                'observers' => $config['observers'] ?? false,
                'policies' => $config['policies'] ?? false,
            ];
        }

        return $modules;
    }
}