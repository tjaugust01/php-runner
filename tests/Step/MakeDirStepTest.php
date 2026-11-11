<?php
declare(strict_types=1);

namespace SetupRunner\Tests\Step;

use PHPUnit\Framework\TestCase;
use SetupRunner\Step\MakeDirStep;
use Symfony\Component\Filesystem\Filesystem;

class MakeDirStepTest extends TestCase
{
    private string $testDir;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->testDir = sys_get_temp_dir() . '/php_runner_makedir_test_' . uniqid();
        $this->filesystem->mkdir($this->testDir);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->testDir);
    }

    public function testMakeDirSuccess(): void
    {
        $newDir = $this->testDir . '/new_folder/sub_folder';
        $step = new MakeDirStep($newDir);
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertDirectoryExists($newDir);
        $this->assertStringContainsString('Verzeichnis erfolgreich erstellt', $result->message);
    }

    public function testMakeDirAlreadyExists(): void
    {
        $existingDir = $this->testDir . '/existing';
        $this->filesystem->mkdir($existingDir);

        $step = new MakeDirStep($existingDir);
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertStringContainsString('Verzeichnis existiert bereits', $result->message);
    }

    public function testMakeDirFailsIfPathIsAFile(): void
    {
        $filePath = $this->testDir . '/somefile.txt';
        file_put_contents($filePath, 'content');

        $step = new MakeDirStep($filePath);
        $result = $step->execute();

        $this->assertFalse($result->success);
        $this->assertStringContainsString('ist aber kein Verzeichnis', $result->message);
    }
}
