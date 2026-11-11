<?php
declare(strict_types=1);

namespace SetupRunner\Tests\Step;

use PHPUnit\Framework\TestCase;
use SetupRunner\Step\CreateFileStep;
use Symfony\Component\Filesystem\Filesystem;

class CreateFileStepTest extends TestCase
{
    private string $testDir;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->testDir = sys_get_temp_dir() . '/php_runner_createfile_test_' . uniqid();
        $this->filesystem->mkdir($this->testDir);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->testDir);
    }

    public function testCreateFileSuccess(): void
    {
        $filePath = $this->testDir . '/nested/directory/test.log';
        $step = new CreateFileStep($filePath, 'sample log content');
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertFileExists($filePath);
        $this->assertEquals('sample log content', file_get_contents($filePath));
    }

    public function testCreateFileSkipIfExists(): void
    {
        $filePath = $this->testDir . '/existing.txt';
        file_put_contents($filePath, 'old content');

        $step = new CreateFileStep($filePath, 'new content', skipIfExists: true);
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertStringContainsString('Erstellen übersprungen', $result->message);
        $this->assertEquals('old content', file_get_contents($filePath));
    }

    public function testCreateFileOverwriteWhenSkipIsFalse(): void
    {
        $filePath = $this->testDir . '/existing.txt';
        file_put_contents($filePath, 'old content');

        $step = new CreateFileStep($filePath, 'new content', skipIfExists: false);
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertEquals('new content', file_get_contents($filePath));
    }
}
