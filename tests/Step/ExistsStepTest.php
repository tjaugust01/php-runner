<?php
declare(strict_types=1);

namespace SetupRunner\Tests\Step;

use PHPUnit\Framework\TestCase;
use SetupRunner\Step\ExistsStep;
use Symfony\Component\Filesystem\Filesystem;

class ExistsStepTest extends TestCase
{
    private string $testDir;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->testDir = sys_get_temp_dir() . '/php_runner_exists_test_' . uniqid();
        $this->filesystem->mkdir($this->testDir);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->testDir);
    }

    public function testExistsSuccessWhenFileExists(): void
    {
        $file = $this->testDir . '/file.txt';
        file_put_contents($file, 'test');

        $step = new ExistsStep($file, expectExists: true);
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertStringContainsString('existiert wie erwartet', $result->message);
    }

    public function testExistsFailsWhenFileMissing(): void
    {
        $file = $this->testDir . '/missing.txt';

        $step = new ExistsStep($file, expectExists: true);
        $result = $step->execute();

        $this->assertFalse($result->success);
        $this->assertStringContainsString('Existenz-Prüfung fehlgeschlagen', $result->message);
    }

    public function testExpectDoesNotExistSuccess(): void
    {
        $file = $this->testDir . '/missing.txt';

        $step = new ExistsStep($file, expectExists: false);
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertStringContainsString('existiert nicht (wie erwartet)', $result->message);
    }
}
