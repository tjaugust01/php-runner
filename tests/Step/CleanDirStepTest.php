<?php
declare(strict_types=1);

namespace SetupRunner\Tests\Step;

use PHPUnit\Framework\TestCase;
use SetupRunner\Step\CleanDirStep;
use Symfony\Component\Filesystem\Filesystem;

class CleanDirStepTest extends TestCase
{
    private string $testDir;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->testDir = sys_get_temp_dir() . '/php_runner_cleandir_test_' . uniqid();
        $this->filesystem->mkdir($this->testDir);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->testDir);
    }

    public function testCleanDirCleansFilesAndDirectories(): void
    {
        file_put_contents($this->testDir . '/file1.txt', 'test');
        file_put_contents($this->testDir . '/.hidden', 'hidden test');
        $this->filesystem->mkdir($this->testDir . '/subfolder');
        file_put_contents($this->testDir . '/subfolder/file2.txt', 'nested');

        $step = new CleanDirStep($this->testDir);
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertDirectoryExists($this->testDir);
        $files = array_diff(scandir($this->testDir), ['.', '..']);
        $this->assertEmpty($files, 'Directory should be completely empty after cleanDir');
    }

    public function testCleanDirNonExistingDirectory(): void
    {
        $nonExistingDir = $this->testDir . '/does_not_exist';
        $step = new CleanDirStep($nonExistingDir);
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertStringContainsString('nichts zu bereinigen', $result->message);
    }
}
