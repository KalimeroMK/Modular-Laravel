<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use Tests\TestCase;

class PolymorphicRelationshipsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private array $generatedFiles = [];

    protected function tearDown(): void
    {
        // Clean up generated test modules
        foreach ($this->generatedFiles as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        // Clean up test module directories
        $testModules = ['TestComment', 'TestTag', 'TestProduct'];
        foreach ($testModules as $module) {
            $modulePath = app_path("Modules/{$module}");
            if (File::exists($modulePath)) {
                File::deleteDirectory($modulePath);
            }
        }

        parent::tearDown();
    }

    public function test_generates_module_with_morphable_fields(): void
    {
        // Generate a Comment module with morphable field
        $exitCode = Artisan::call('make:module', [
            'name' => 'TestComment',
            '--model' => 'content:text,commentable:morphable',
            '--relations' => 'commentable:morphTo,user:belongsTo:User',
        ]);

        $this->assertEquals(0, $exitCode);

        // Check that the model file was created
        $modelPath = app_path('Modules/TestComment/Models/TestComment.php');
        $this->assertTrue(File::exists($modelPath));

        // Check model content
        $modelContent = File::get($modelPath);
        $this->assertStringContainsString('commentable_type', $modelContent);
        $this->assertStringContainsString('commentable_id', $modelContent);
        $this->assertStringContainsString('public function commentable()', $modelContent);
        $this->assertStringContainsString('return $this->morphTo()', $modelContent);

        // Check migration was created
        $migrationsPath = app_path('Modules/TestComment/Database/migrations');
        $migrationFiles = File::glob($migrationsPath.'/*_create_test_comments_table.php');
        $this->assertNotEmpty($migrationFiles);

        $migrationContent = File::get($migrationFiles[0]);
        $this->assertStringContainsString('$table->morphs(\'commentable\')', $migrationContent);

        $this->generatedFiles[] = $modelPath;
        $this->generatedFiles = array_merge($this->generatedFiles, $migrationFiles);
    }

    public function test_generates_module_with_morph_many_relation(): void
    {
        // Generate a Product module with morphMany relation
        $exitCode = Artisan::call('make:module', [
            'name' => 'TestProduct',
            '--model' => 'name:string,price:float',
            '--relations' => 'comments:morphMany:TestComment:commentable,tags:morphToMany:TestTag:taggable',
        ]);

        $this->assertEquals(0, $exitCode);

        $modelPath = app_path('Modules/TestProduct/Models/TestProduct.php');
        $this->assertTrue(File::exists($modelPath));

        $modelContent = File::get($modelPath);
        $this->assertStringContainsString('public function comments()', $modelContent);
        $this->assertStringContainsString('return $this->morphMany(TestComment::class, \'commentable\')', $modelContent);
        $this->assertStringContainsString('public function tags()', $modelContent);
        $this->assertStringContainsString('return $this->morphToMany(TestTag::class, \'taggable\')', $modelContent);

        $this->generatedFiles[] = $modelPath;
    }

    public function test_generates_module_from_yaml_with_polymorphic_relations(): void
    {
        // Create a temporary YAML file
        $yamlContent = '
modules:
  TestTag:
    fields:
      name: string
      slug: string
    relations:
      morphToMany: 
        - model: TestProduct
          morph_name: taggable
    exceptions: false
    observers: false
    policies: false
        ';

        $yamlFile = storage_path('test_modules.yaml');
        File::put($yamlFile, $yamlContent);

        // Generate modules from YAML
        $exitCode = Artisan::call('modules:build-from-yaml', [
            'file' => $yamlFile,
        ]);

        $this->assertEquals(0, $exitCode);

        // Check that the model was created correctly
        $modelPath = app_path('Modules/TestTag/Models/TestTag.php');
        $this->assertTrue(File::exists($modelPath));

        $modelContent = File::get($modelPath);
        $this->assertStringContainsString('public function TestProduct()', $modelContent);
        $this->assertStringContainsString('return $this->morphToMany(TestProduct::class, \'taggable\')', $modelContent);

        // Clean up
        File::delete($yamlFile);
        $this->generatedFiles[] = $modelPath;
    }

    public function test_field_parser_with_morphable_fields(): void
    {
        $parser = app(\App\Modules\Core\Support\Generators\FieldParser::class);

        $result = $parser->parse('content:text,owner:morphable,active:bool');

        $this->assertCount(4, $result); // content + owner_type + owner_id + active

        // Check morphable fields
        $ownerTypeField = collect($result)->where('name', 'owner_type')->first();
        $ownerIdField = collect($result)->where('name', 'owner_id')->first();

        $this->assertNotNull($ownerTypeField);
        $this->assertNotNull($ownerIdField);
        $this->assertEquals('string', $ownerTypeField['type']);
        $this->assertEquals('int', $ownerIdField['type']);
        $this->assertEquals('owner', $ownerTypeField['morphable_name']);
        $this->assertEquals('owner', $ownerIdField['morphable_name']);
    }

    public function test_migration_fields_generation_with_morphs(): void
    {
        $stubGenerator = app(\App\Modules\Core\Support\Generators\StubFileGenerator::class);

        // Mock fields with morphable
        $fields = [
            ['name' => 'content', 'type' => 'string'],
            ['name' => 'owner_type', 'type' => 'string', 'morphable_name' => 'owner'],
            ['name' => 'owner_id', 'type' => 'int', 'morphable_name' => 'owner'],
            ['name' => 'active', 'type' => 'bool'],
        ];

        // Use reflection to test protected method
        $reflection = new ReflectionClass($stubGenerator);
        $method = $reflection->getMethod('buildMigrationFields');
        $method->setAccessible(true);

        $result = $method->invoke($stubGenerator, $fields);

        $this->assertStringContainsString('$table->string(\'content\')', $result);
        $this->assertStringContainsString('$table->morphs(\'owner\')', $result);
        $this->assertStringContainsString('$table->boolean(\'active\')', $result);

        // Should only have one morphs() call even though we have both type and id fields
        $this->assertEquals(1, mb_substr_count($result, '$table->morphs(\'owner\')'));
    }

    public function test_yaml_parser_handles_complex_polymorphic_structure(): void
    {
        $yamlContent = '
modules:
  Article:
    fields:
      title: string
      content: text
    relations:
      morphMany:
        - model: Comment
          morph_name: commentable
      morphToMany:
        - model: Tag
          morph_name: taggable
      morphTo:
        name: parent
    exceptions: true
        ';

        $yamlFile = storage_path('test_complex.yaml');
        File::put($yamlFile, $yamlContent);

        $parser = new \App\Modules\Core\Support\YamlModule\YamlModuleParser($yamlFile);
        $result = $parser->parse();

        $this->assertArrayHasKey('Article', $result);

        $relations = $result['Article']['relations'];
        $this->assertContains('Comment:morphMany:Comment:commentable', $relations);
        $this->assertContains('Tag:morphToMany:Tag:taggable', $relations);
        $this->assertContains('parent:morphTo', $relations);

        File::delete($yamlFile);
    }

    public function test_relationship_building_with_polymorphic_types(): void
    {
        $makeModuleCommand = new \App\Console\Commands\MakeModuleCommand();

        // Use reflection to test protected method
        $reflection = new ReflectionClass($makeModuleCommand);
        $method = $reflection->getMethod('buildPolymorphicRelationship');
        $method->setAccessible(true);

        // Test morphTo
        $result = $method->invoke($makeModuleCommand, 'owner', 'morphTo', 'Owner', ['owner', 'morphTo']);
        $this->assertStringContainsString('return $this->morphTo()', $result);

        // Test morphMany
        $result = $method->invoke($makeModuleCommand, 'comments', 'morphMany', 'Comment', ['comments', 'morphMany', 'Comment', 'commentable']);
        $this->assertStringContainsString('return $this->morphMany(Comment::class, \'commentable\')', $result);

        // Test morphToMany
        $result = $method->invoke($makeModuleCommand, 'tags', 'morphToMany', 'Tag', ['tags', 'morphToMany', 'Tag', 'taggable']);
        $this->assertStringContainsString('return $this->morphToMany(Tag::class, \'taggable\')', $result);

        // Test morphOne
        $result = $method->invoke($makeModuleCommand, 'avatar', 'morphOne', 'Avatar', ['avatar', 'morphOne', 'Avatar', 'imageable']);
        $this->assertStringContainsString('return $this->morphOne(Avatar::class, \'imageable\')', $result);
    }

    public function test_full_workflow_generates_working_polymorphic_models(): void
    {
        // This test validates that the complete workflow creates working models

        // 1. Generate base models
        Artisan::call('make:module', [
            'name' => 'TestComment',
            '--model' => 'content:text,commentable:morphable',
            '--relations' => 'commentable:morphTo',
        ]);

        Artisan::call('make:module', [
            'name' => 'TestProduct',
            '--model' => 'name:string',
            '--relations' => 'comments:morphMany:TestComment:commentable',
        ]);

        // 2. Check that model files exist and contain expected content
        $commentModelPath = app_path('Modules/TestComment/Models/TestComment.php');
        $productModelPath = app_path('Modules/TestProduct/Models/TestProduct.php');

        $this->assertTrue(File::exists($commentModelPath));
        $this->assertTrue(File::exists($productModelPath));

        // 3. Verify model content
        $commentContent = File::get($commentModelPath);
        $productContent = File::get($productModelPath);

        // Comment model should have morphTo relationship
        $this->assertStringContainsString('public function commentable()', $commentContent);
        $this->assertStringContainsString('return $this->morphTo()', $commentContent);
        $this->assertStringContainsString('commentable_type', $commentContent);
        $this->assertStringContainsString('commentable_id', $commentContent);

        // Product model should have morphMany relationship
        $this->assertStringContainsString('public function comments()', $productContent);
        $this->assertStringContainsString('return $this->morphMany(TestComment::class, \'commentable\')', $productContent);

        // 4. Check migrations exist
        $commentMigrations = File::glob(app_path('Modules/TestComment/Database/migrations/*_create_test_comments_table.php'));
        $productMigrations = File::glob(app_path('Modules/TestProduct/Database/migrations/*_create_test_products_table.php'));

        $this->assertNotEmpty($commentMigrations);
        $this->assertNotEmpty($productMigrations);

        // 5. Verify migration content
        $commentMigrationContent = File::get($commentMigrations[0]);
        $this->assertStringContainsString('$table->morphs(\'commentable\')', $commentMigrationContent);

        $this->generatedFiles[] = $commentModelPath;
        $this->generatedFiles[] = $productModelPath;
        $this->generatedFiles = array_merge($this->generatedFiles, $commentMigrations, $productMigrations);
    }
}
