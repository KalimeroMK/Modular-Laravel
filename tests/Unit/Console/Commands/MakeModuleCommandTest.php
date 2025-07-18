<?php

declare(strict_types=1);

namespace Tests\Unit\Console\Commands;

use App\Console\Commands\MakeModuleCommand;
use PHPUnit\Framework\TestCase;

class MakeModuleCommandTest extends TestCase
{
    private MakeModuleCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new MakeModuleCommand();
    }

    public function test_builds_standard_relationships(): void
    {
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('buildRelationships');
        $method->setAccessible(true);

        $result = $method->invoke($this->command, 'user:belongsTo:User,categories:belongsToMany:Category');

        $this->assertStringContainsString('public function user()', $result);
        $this->assertStringContainsString('return $this->belongsTo(User::class)', $result);
        $this->assertStringContainsString('public function categories()', $result);
        $this->assertStringContainsString('return $this->belongsToMany(Category::class)', $result);
    }

    public function test_builds_morph_to_relationship(): void
    {
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('buildPolymorphicRelationship');
        $method->setAccessible(true);

        $result = $method->invoke($this->command, 'owner', 'morphTo', 'Owner', ['owner', 'morphTo']);

        $expected = "    public function owner()\n    {\n        return \$this->morphTo();\n    }\n";
        $this->assertEquals($expected, $result);
    }

    public function test_builds_morph_many_relationship(): void
    {
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('buildPolymorphicRelationship');
        $method->setAccessible(true);

        $result = $method->invoke($this->command, 'comments', 'morphMany', 'Comment', ['comments', 'morphMany', 'Comment', 'commentable']);

        $expected = "    public function comments()\n    {\n        return \$this->morphMany(Comment::class, 'commentable');\n    }\n";
        $this->assertEquals($expected, $result);
    }

    public function test_builds_morph_one_relationship(): void
    {
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('buildPolymorphicRelationship');
        $method->setAccessible(true);

        $result = $method->invoke($this->command, 'avatar', 'morphOne', 'Avatar', ['avatar', 'morphOne', 'Avatar', 'imageable']);

        $expected = "    public function avatar()\n    {\n        return \$this->morphOne(Avatar::class, 'imageable');\n    }\n";
        $this->assertEquals($expected, $result);
    }

    public function test_builds_morph_to_many_relationship(): void
    {
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('buildPolymorphicRelationship');
        $method->setAccessible(true);

        $result = $method->invoke($this->command, 'tags', 'morphToMany', 'Tag', ['tags', 'morphToMany', 'Tag', 'taggable']);

        $expected = "    public function tags()\n    {\n        return \$this->morphToMany(Tag::class, 'taggable');\n    }\n";
        $this->assertEquals($expected, $result);
    }

    public function test_builds_morph_many_with_default_morph_name(): void
    {
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('buildPolymorphicRelationship');
        $method->setAccessible(true);

        // When no morph name is provided, it should use the relation name
        $result = $method->invoke($this->command, 'comments', 'morphMany', 'Comment', ['comments', 'morphMany', 'Comment']);

        $expected = "    public function comments()\n    {\n        return \$this->morphMany(Comment::class, 'comments');\n    }\n";
        $this->assertEquals($expected, $result);
    }

    public function test_builds_mixed_relationships(): void
    {
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('buildRelationships');
        $method->setAccessible(true);

        $relations = 'user:belongsTo:User,comments:morphMany:Comment:commentable,tags:morphToMany:Tag:taggable,owner:morphTo';
        $result = $method->invoke($this->command, $relations);

        // Check standard relationship
        $this->assertStringContainsString('public function user()', $result);
        $this->assertStringContainsString('return $this->belongsTo(User::class)', $result);

        // Check morphMany relationship
        $this->assertStringContainsString('public function comments()', $result);
        $this->assertStringContainsString('return $this->morphMany(Comment::class, \'commentable\')', $result);

        // Check morphToMany relationship
        $this->assertStringContainsString('public function tags()', $result);
        $this->assertStringContainsString('return $this->morphToMany(Tag::class, \'taggable\')', $result);

        // Check morphTo relationship
        $this->assertStringContainsString('public function owner()', $result);
        $this->assertStringContainsString('return $this->morphTo()', $result);
    }

    public function test_handles_empty_relations(): void
    {
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('buildRelationships');
        $method->setAccessible(true);

        $result = $method->invoke($this->command, '');

        $this->assertEquals('', $result);
    }

    public function test_handles_invalid_relation_format(): void
    {
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('buildRelationships');
        $method->setAccessible(true);

        // Relations without proper format should be skipped
        $result = $method->invoke($this->command, 'invalid,user:belongsTo:User');

        $this->assertStringNotContainsString('invalid', $result);
        $this->assertStringContainsString('public function user()', $result);
        $this->assertStringContainsString('return $this->belongsTo(User::class)', $result);
    }

    public function test_falls_back_to_standard_relationship_for_unknown_morph_type(): void
    {
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('buildPolymorphicRelationship');
        $method->setAccessible(true);

        $result = $method->invoke($this->command, 'test', 'unknownType', 'TestModel', ['test', 'unknownType', 'TestModel']);

        $expected = "    public function test()\n    {\n        return \$this->unknownType(TestModel::class);\n    }\n";
        $this->assertEquals($expected, $result);
    }

    public function test_parses_fields_correctly(): void
    {
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('parseFields');
        $method->setAccessible(true);

        $result = $method->invoke($this->command, 'name:string,age:int,active:bool');

        $expected = [
            ['name' => 'name', 'type' => 'string'],
            ['name' => 'age', 'type' => 'int'],
            ['name' => 'active', 'type' => 'bool']
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_handles_empty_model_fields(): void
    {
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('parseFields');
        $method->setAccessible(true);

        $result = $method->invoke($this->command, '');

        $this->assertEquals([], $result);
    }

    public function test_builds_relationships_with_proper_formatting(): void
    {
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('buildRelationships');
        $method->setAccessible(true);

        $result = $method->invoke($this->command, 'comments:morphMany:Comment:commentable');

        // Check for proper indentation and formatting
        $this->assertStringContainsString('    public function comments()', $result);
        $this->assertStringContainsString('    {', $result);
        $this->assertStringContainsString('        return $this->morphMany(Comment::class, \'commentable\');', $result);
        $this->assertStringContainsString('    }', $result);
    }

    public function test_handles_complex_polymorphic_scenario(): void
    {
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('buildRelationships');
        $method->setAccessible(true);

        $relations = 'commentable:morphTo,comments:morphMany:Comment:commentable,tags:morphToMany:Tag:taggable,avatar:morphOne:Avatar:imageable';
        $result = $method->invoke($this->command, $relations);

        // Verify all polymorphic relationships are built correctly
        $lines = explode("\n", $result);
        $methodCount = 0;
        
        foreach ($lines as $line) {
            if (strpos($line, 'public function') !== false) {
                $methodCount++;
            }
        }

        $this->assertEquals(4, $methodCount); // Should have 4 relationship methods

        // Check specific content
        $this->assertStringContainsString('morphTo()', $result);
        $this->assertStringContainsString('morphMany(Comment::class, \'commentable\')', $result);
        $this->assertStringContainsString('morphToMany(Tag::class, \'taggable\')', $result);
        $this->assertStringContainsString('morphOne(Avatar::class, \'imageable\')', $result);
    }
} 