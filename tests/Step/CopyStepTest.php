<?php
declare(strict_types=1);

namespace SetupRunner\Tests\Step;

use PHPUnit\Framework\TestCase;
use SetupRunner\Step\CopyStep;
use Symfony\Component\Filesystem\Filesystem;

class CopyStepTest extends TestCase
{
    private string $testDir;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->testDir = sys_get_temp_dir() . '/php_runner_test_' . uniqid();
        $this->filesystem->mkdir($this->testDir);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->testDir);
    }

    public function testCopyFileSuccess(): void
    {
        $source = $this->testDir . '/source.txt';
        $target = $this->testDir . '/target.txt';
        file_put_contents($source, 'Hello World');

        $step = new CopyStep($source, $target);
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertFileExists($target);
        $this->assertEquals('Hello World', file_get_contents($target));
    }

    public function testCopyFileSkipIfExists(): void
    {
        $source = $this->testDir . '/source.txt';
        $target = $this->testDir . '/target.txt';
        file_put_contents($source, 'New Content');
        file_put_contents($target, 'Original Content');

        $step = new CopyStep($source, $target, skipIfExists: true);
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertStringContainsString('Kopieren übersprungen', $result->message);
        $this->assertEquals('Original Content', file_get_contents($target));
    }

    public function testCopyFileOverwriteWhenSkipIsFalse(): void
    {
        $source = $this->testDir . '/source.txt';
        $target = $this->testDir . '/target.txt';
        file_put_contents($source, 'New Content');
        file_put_contents($target, 'Original Content');

        $step = new CopyStep($source, $target, skipIfExists: false);
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertEquals('New Content', file_get_contents($target));
    }

    public function testCopyDirectoryRecursively(): void
    {
        $sourceDir = $this->testDir . '/src_dir';
        $targetDir = $this->testDir . '/dst_dir';
        $this->filesystem->mkdir($sourceDir . '/sub');
        file_put_contents($sourceDir . '/sub/file.txt', 'Nested Content');

        $step = new CopyStep($sourceDir, $targetDir);
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertFileExists($targetDir . '/sub/file.txt');
        $this->assertEquals('Nested Content', file_get_contents($targetDir . '/sub/file.txt'));
    }

    public function testCopyFailsWhenSourceDoesNotExist(): void
    {
        $source = $this->testDir . '/non_existing.txt';
        $target = $this->testDir . '/target.txt';

        $step = new CopyStep($source, $target);
        $result = $step->execute();

        $this->assertFalse($result->success);
        $this->assertStringContainsString('Quellpfad existiert nicht', $result->message);
    }
}
