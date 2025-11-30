<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Support\YamlModule;

use App\Modules\Core\Support\YamlModule\YamlModuleParser;
use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class YamlModuleParserTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempFile = tempnam(sys_get_temp_dir(), 'yaml_test');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
        Mockery::close();
        parent::tearDown();
    }

    public function test_parses_basic_module_structure(): void
    {
        $data = [
            'modules' => [
                'User' => [
                    'fields' => [
                        'name' => 'string',
                        'email' => 'string',
                    ],
                    'relations' => [
                        'belongsTo' => 'Role',
                    ],
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $this->assertArrayHasKey('User', $result);
        $this->assertEquals(['name:string', 'email:string'], $result['User']['fields']);
        $this->assertEquals(['belongsTo:Role'], $result['User']['relations']);
    }

    public function test_parses_polymorphic_morph_to_relations(): void
    {
        $data = [
            'modules' => [
                'Comment' => [
                    'fields' => ['content' => 'text'],
                    'relations' => [
                        'morphTo' => ['name' => 'commentable'],
                    ],
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $this->assertEquals(['commentable:morphTo'], $result['Comment']['relations']);
    }

    public function test_parses_polymorphic_morph_many_relations_array(): void
    {
        $data = [
            'modules' => [
                'Product' => [
                    'fields' => ['name' => 'string'],
                    'relations' => [
                        'morphMany' => [
                            ['model' => 'Comment', 'morph_name' => 'commentable'],
                        ],
                    ],
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $this->assertEquals(['Comment:morphMany:Comment:commentable'], $result['Product']['relations']);
    }

    public function test_parses_polymorphic_morph_to_many_relations(): void
    {
        $data = [
            'modules' => [
                'Product' => [
                    'fields' => ['name' => 'string'],
                    'relations' => [
                        'morphToMany' => [
                            ['model' => 'Tag', 'morph_name' => 'taggable'],
                        ],
                    ],
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $this->assertEquals(['Tag:morphToMany:Tag:taggable'], $result['Product']['relations']);
    }

    public function test_parses_multiple_polymorphic_relations(): void
    {
        $data = [
            'modules' => [
                'Post' => [
                    'fields' => ['title' => 'string', 'content' => 'text'],
                    'relations' => [
                        'morphMany' => [
                            ['model' => 'Comment', 'morph_name' => 'commentable'],
                        ],
                        'morphToMany' => [
                            ['model' => 'Tag', 'morph_name' => 'taggable'],
                        ],
                    ],
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $expected = [
            'Comment:morphMany:Comment:commentable',
            'Tag:morphToMany:Tag:taggable',
        ];
        $this->assertEquals($expected, $result['Post']['relations']);
    }

    public function test_parses_string_relations(): void
    {
        $data = [
            'modules' => [
                'User' => [
                    'fields' => ['name' => 'string'],
                    'relations' => [
                        'belongsTo' => 'Role',
                    ],
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $this->assertEquals(['belongsTo:Role'], $result['User']['relations']);
    }

    public function test_parses_array_relations_simple_strings(): void
    {
        $data = [
            'modules' => [
                'Product' => [
                    'fields' => ['name' => 'string'],
                    'relations' => [
                        'belongsToMany' => ['Category', 'Tag'],
                    ],
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $expected = ['Category:belongsToMany', 'Tag:belongsToMany'];
        $this->assertEquals($expected, $result['Product']['relations']);
    }

    public function test_parses_mixed_relation_types(): void
    {
        $data = [
            'modules' => [
                'Article' => [
                    'fields' => ['title' => 'string'],
                    'relations' => [
                        'belongsTo' => 'User',
                        'belongsToMany' => ['Category'],
                        'morphMany' => [
                            ['model' => 'Comment', 'morph_name' => 'commentable'],
                        ],
                        'morphTo' => ['name' => 'parent'],
                    ],
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $expected = [
            'belongsTo:User',
            'Category:belongsToMany',
            'Comment:morphMany:Comment:commentable',
            'parent:morphTo',
        ];
        $this->assertEquals($expected, $result['Article']['relations']);
    }

    public function test_parses_morph_one_relations(): void
    {
        $data = [
            'modules' => [
                'User' => [
                    'fields' => ['name' => 'string'],
                    'relations' => [
                        'morphOne' => [
                            ['model' => 'Avatar', 'morph_name' => 'imageable'],
                        ],
                    ],
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $this->assertEquals(['Avatar:morphOne:Avatar:imageable'], $result['User']['relations']);
    }

    public function test_handles_morph_relations_without_morph_name(): void
    {
        $data = [
            'modules' => [
                'Product' => [
                    'fields' => ['name' => 'string'],
                    'relations' => [
                        'morphMany' => [
                            ['model' => 'Comment'],
                        ],
                    ],
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $this->assertEquals(['Comment:morphMany'], $result['Product']['relations']);
    }

    public function test_handles_morph_relations_with_name_fallback(): void
    {
        $data = [
            'modules' => [
                'Product' => [
                    'fields' => ['name' => 'string'],
                    'relations' => [
                        'morphMany' => [
                            ['model' => 'Comment', 'name' => 'commentable'],
                        ],
                    ],
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $this->assertEquals(['Comment:morphMany:Comment:commentable'], $result['Product']['relations']);
    }

    public function test_preserves_other_module_options(): void
    {
        $data = [
            'modules' => [
                'User' => [
                    'fields' => ['name' => 'string'],
                    'relations' => ['belongsTo' => 'Role'],
                    'exceptions' => true,
                    'observers' => true,
                    'policies' => false,
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $this->assertTrue($result['User']['exceptions']);
        $this->assertTrue($result['User']['observers']);
        $this->assertFalse($result['User']['policies']);
    }

    public function test_throws_exception_when_modules_key_missing(): void
    {
        $data = ['invalid' => 'structure'];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("YAML must contain 'modules' key.");

        $parser->parse();
    }

    public function test_handles_empty_relations(): void
    {
        $data = [
            'modules' => [
                'Simple' => [
                    'fields' => ['name' => 'string'],
                    'relations' => [],
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $this->assertEquals([], $result['Simple']['relations']);
    }

    public function test_handles_missing_relations(): void
    {
        $data = [
            'modules' => [
                'Simple' => [
                    'fields' => ['name' => 'string'],
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $this->assertEquals([], $result['Simple']['relations']);
    }

    public function test_parses_events_option(): void
    {
        $data = [
            'modules' => [
                'Product' => [
                    'fields' => ['name' => 'string'],
                    'events' => true,
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $this->assertTrue($result['Product']['events']);
    }

    public function test_parses_enum_option(): void
    {
        $data = [
            'modules' => [
                'Product' => [
                    'fields' => ['name' => 'string'],
                    'enum' => true,
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $this->assertTrue($result['Product']['enum']);
    }

    public function test_parses_notifications_option(): void
    {
        $data = [
            'modules' => [
                'Product' => [
                    'fields' => ['name' => 'string'],
                    'notifications' => true,
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $this->assertTrue($result['Product']['notifications']);
    }

    public function test_parses_all_options_together(): void
    {
        $data = [
            'modules' => [
                'Product' => [
                    'fields' => ['name' => 'string'],
                    'exceptions' => true,
                    'observers' => true,
                    'policies' => true,
                    'events' => true,
                    'enum' => true,
                    'notifications' => false,
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $this->assertTrue($result['Product']['exceptions']);
        $this->assertTrue($result['Product']['observers']);
        $this->assertTrue($result['Product']['policies']);
        $this->assertTrue($result['Product']['events']);
        $this->assertTrue($result['Product']['enum']);
        $this->assertFalse($result['Product']['notifications']);
    }

    public function test_parses_morph_relation_string_format(): void
    {
        $data = [
            'modules' => [
                'Product' => [
                    'fields' => ['name' => 'string'],
                    'relations' => [
                        'comments' => 'morphMany:Comment:commentable',
                    ],
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $this->assertEquals(['comments:morphMany:Comment:commentable'], $result['Product']['relations']);
    }

    public function test_parses_morph_to_string_format(): void
    {
        $data = [
            'modules' => [
                'Comment' => [
                    'fields' => ['body' => 'text'],
                    'relations' => [
                        'commentable' => 'morphTo',
                    ],
                ],
            ],
        ];

        $this->createYamlFile($data);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        // When commentable is the key and morphTo is the value, it should parse as commentable:morphTo
        $this->assertContains('commentable:morphTo', $result['Comment']['relations']);
    }

    private function createYamlFile(array $data): void
    {
        file_put_contents($this->tempFile, Yaml::dump($data));
    }
}
