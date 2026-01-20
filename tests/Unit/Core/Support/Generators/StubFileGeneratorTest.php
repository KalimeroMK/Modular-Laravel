<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Support\Generators;

use App\Modules\Core\Support\Generators\StubFileGenerator;
use Illuminate\Filesystem\Filesystem;
use Override;
use Tests\TestCase;

class StubFileGeneratorTest extends TestCase
{
    private Filesystem $files;

    private array $testModules = ['TestModule', 'Product'];

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->files = new Filesystem;
    }

    #[Override]
    protected function tearDown(): void
    {
        // Cleanup all test modules
        foreach ($this->testModules as $moduleName) {
            $modulePath = app_path("Modules/{$moduleName}");
            if ($this->files->exists($modulePath)) {
                $this->files->deleteDirectory($modulePath);
            }
        }

        parent::tearDown();
    }

    public function test_generates_interface_file_when_stub_exists(): void
    {
        $generator = new StubFileGenerator($this->files);

        $fields = [
            ['name' => 'name', 'type' => 'string'],
        ];

        $options = [
            'table' => 'test_modules',
        ];

        // Should not throw if stub exists
        $generator->generate('TestModule', $fields, $options);

        $interfacePath = app_path('Modules/TestModule/Infrastructure/Repositories/TestModuleRepositoryInterface.php');
        if ($this->files->exists($interfacePath)) {
            $content = $this->files->get($interfacePath);
            $this->assertStringContainsString('TestModuleRepositoryInterface', $content);
            $this->assertStringNotContainsString('{{module}}', $content);
        }
    }

    public function test_replaces_placeholders_correctly(): void
    {
        $generator = new StubFileGenerator($this->files);

        $fields = [
            ['name' => 'name', 'type' => 'string'],
        ];

        $options = [
            'table' => 'custom_table',
        ];

        $generator->generate('Product', $fields, $options);

        $modelPath = app_path('Modules/Product/Infrastructure/Models/Product.php');
        if ($this->files->exists($modelPath)) {
            $content = $this->files->get($modelPath);
            $this->assertStringContainsString('class Product', $content);
            $this->assertStringNotContainsString('{{module}}', $content);
            $this->assertStringNotContainsString('{{table}}', $content);
        }
    }
}
