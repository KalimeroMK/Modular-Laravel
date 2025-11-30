<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Support\YamlModule;

use App\Modules\Core\Support\YamlModule\YamlModuleParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class YamlModuleParserCommentsTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempFile = tempnam(sys_get_temp_dir(), 'yaml_comments_test');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
        parent::tearDown();
    }

    public function test_ignores_comments_in_yaml_file(): void
    {
        // Symfony YAML parser automatically ignores comments
        $yamlContent = <<<'YAML'
# This is a comment
modules:
  # Another comment
  Product:
    # Field comment
    fields:
      name: string  # Inline comment
      price: float
    # Relations comment
    relations:
      belongsToMany: [Category]
    # Options comment
    observers: true
YAML;

        file_put_contents($this->tempFile, $yamlContent);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        // Should parse successfully despite comments
        $this->assertArrayHasKey('Product', $result);
        $this->assertEquals(['name:string', 'price:float'], $result['Product']['fields']);
        $this->assertTrue($result['Product']['observers']);
    }

    public function test_handles_multiline_comments(): void
    {
        $yamlContent = <<<'YAML'
# Multi-line comment
# This is a test module
# With multiple comment lines
modules:
  TestModule:
    fields:
      name: string
YAML;

        file_put_contents($this->tempFile, $yamlContent);
        $parser = new YamlModuleParser($this->tempFile);
        $result = $parser->parse();

        $this->assertArrayHasKey('TestModule', $result);
    }
}
