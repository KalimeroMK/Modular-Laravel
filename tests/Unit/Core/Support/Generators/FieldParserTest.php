<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Support\Generators;

use App\Modules\Core\Support\Generators\FieldParser;
use PHPUnit\Framework\TestCase;

class FieldParserTest extends TestCase
{
    private FieldParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new FieldParser();
    }

    public function test_parses_basic_string_fields(): void
    {
        $result = $this->parser->parse('name:string,email:string');

        $this->assertCount(2, $result);
        $this->assertEquals([
            ['name' => 'name', 'type' => 'string'],
            ['name' => 'email', 'type' => 'string']
        ], $result);
    }

    public function test_parses_various_field_types(): void
    {
        $result = $this->parser->parse('age:int,price:float,active:bool,data:json');

        $this->assertEquals([
            ['name' => 'age', 'type' => 'int'],
            ['name' => 'price', 'type' => 'float'],
            ['name' => 'active', 'type' => 'bool'],
            ['name' => 'data', 'type' => 'array']
        ], $result);
    }

    public function test_parses_foreign_key_fields(): void
    {
        $result = $this->parser->parse('user_id:foreign:users:id');

        $this->assertEquals([
            [
                'name' => 'user_id',
                'type' => 'foreign',
                'references' => 'id',
                'on' => 'users'
            ]
        ], $result);
    }

    public function test_parses_foreign_key_with_defaults(): void
    {
        $result = $this->parser->parse('user_id:foreign');

        $this->assertEquals([
            [
                'name' => 'user_id',
                'type' => 'foreign',
                'references' => 'id',
                'on' => 'users'
            ]
        ], $result);
    }

    public function test_parses_morphable_fields(): void
    {
        $result = $this->parser->parse('owner:morphable');

        $this->assertCount(2, $result);
        $this->assertEquals([
            [
                'name' => 'owner_type',
                'type' => 'string',
                'morphable_name' => 'owner'
            ],
            [
                'name' => 'owner_id',
                'type' => 'int',
                'morphable_name' => 'owner'
            ]
        ], $result);
    }

    public function test_parses_morphable_fields_with_custom_name(): void
    {
        $result = $this->parser->parse('field:morphable:commentable');

        $this->assertCount(2, $result);
        $this->assertEquals([
            [
                'name' => 'commentable_type',
                'type' => 'string',
                'morphable_name' => 'commentable'
            ],
            [
                'name' => 'commentable_id',
                'type' => 'int',
                'morphable_name' => 'commentable'
            ]
        ], $result);
    }

    public function test_parses_mixed_field_types(): void
    {
        $result = $this->parser->parse('name:string,user_id:foreign:users,owner:morphable,active:bool');

        $this->assertCount(5, $result);
        
        // String field
        $this->assertEquals(['name' => 'name', 'type' => 'string'], $result[0]);
        
        // Foreign key field
        $this->assertEquals([
            'name' => 'user_id',
            'type' => 'foreign',
            'references' => 'id',
            'on' => 'users'
        ], $result[1]);
        
        // Morphable fields (type and id)
        $this->assertEquals([
            'name' => 'owner_type',
            'type' => 'string',
            'morphable_name' => 'owner'
        ], $result[2]);
        
        $this->assertEquals([
            'name' => 'owner_id',
            'type' => 'int',
            'morphable_name' => 'owner'
        ], $result[3]);
        
        // Boolean field
        $this->assertEquals(['name' => 'active', 'type' => 'bool'], $result[4]);
    }

    public function test_handles_empty_input(): void
    {
        $result = $this->parser->parse('');

        $this->assertCount(1, $result);
        $this->assertEquals([
            ['name' => '', 'type' => 'string']
        ], $result);
    }

    public function test_defaults_to_string_type(): void
    {
        $result = $this->parser->parse('title');

        $this->assertEquals([
            ['name' => 'title', 'type' => 'string']
        ], $result);
    }

    public function test_handles_integer_variations(): void
    {
        $result = $this->parser->parse('a:int,b:integer,c:bigint,d:smallint,e:tinyint,f:foreignId');

        foreach ($result as $field) {
            $this->assertEquals('int', $field['type']);
        }
    }

    public function test_handles_boolean_variations(): void
    {
        $result = $this->parser->parse('a:bool,b:boolean');

        foreach ($result as $field) {
            $this->assertEquals('bool', $field['type']);
        }
    }

    public function test_handles_float_variations(): void
    {
        $result = $this->parser->parse('a:float,b:double,c:decimal');

        foreach ($result as $field) {
            $this->assertEquals('float', $field['type']);
        }
    }

    public function test_handles_array_variations(): void
    {
        $result = $this->parser->parse('a:json,b:array');

        foreach ($result as $field) {
            $this->assertEquals('array', $field['type']);
        }
    }

    public function test_multiple_morphable_fields(): void
    {
        $result = $this->parser->parse('owner:morphable,commentable:morphable:target');

        $this->assertCount(4, $result);
        
        // First morphable
        $this->assertEquals('owner_type', $result[0]['name']);
        $this->assertEquals('owner', $result[0]['morphable_name']);
        $this->assertEquals('owner_id', $result[1]['name']);
        $this->assertEquals('owner', $result[1]['morphable_name']);
        
        // Second morphable with custom name
        $this->assertEquals('target_type', $result[2]['name']);
        $this->assertEquals('target', $result[2]['morphable_name']);
        $this->assertEquals('target_id', $result[3]['name']);
        $this->assertEquals('target', $result[3]['morphable_name']);
    }
} 