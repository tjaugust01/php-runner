<?php
declare(strict_types=1);

namespace SetupRunner\Tests\Step;

use PHPUnit\Framework\TestCase;
use SetupRunner\Step\AppendToFileStep;
use Symfony\Component\Filesystem\Filesystem;

class AppendToFileStepTest extends TestCase
{
    private string $testDir;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->testDir = sys_get_temp_dir() . '/php_runner_append_test_' . uniqid();
        $this->filesystem->mkdir($this->testDir);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->testDir);
    }

    public function testAppendToFileSuccess(): void
    {
        $file = $this->testDir . '/app.log';
        file_put_contents($file, "Line 1\n");

        $step = new AppendToFileStep($file, "Line 2\n");
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertEquals("Line 1\nLine 2\n", file_get_contents($file));
    }
}
