<?php
declare(strict_types=1);

namespace SetupRunner\Tests\Step;

use PHPUnit\Framework\TestCase;
use SetupRunner\Step\ChmodStep;
use Symfony\Component\Filesystem\Filesystem;

class ChmodStepTest extends TestCase
{
    private string $testDir;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->testDir = sys_get_temp_dir() . '/php_runner_chmod_test_' . uniqid();
        $this->filesystem->mkdir($this->testDir);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->testDir);
    }

    public function testChmodExecutableMode(): void
    {
        $script = $this->testDir . '/script.sh';
        file_put_contents($script, "#!/bin/bash\necho ok");

        $step = new ChmodStep($script, mode: 0755);
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertTrue(is_executable($script));
    }

    public function testChmodStringMode(): void
    {
        $script = $this->testDir . '/script.sh';
        file_put_contents($script, "#!/bin/bash\necho ok");

        $step = new ChmodStep($script, mode: '0755');
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertTrue(is_executable($script));
    }

    public function testChmodNonExistingFile(): void
    {
        $missing = $this->testDir . '/missing.sh';
        $step = new ChmodStep($missing, mode: 0755);
        $result = $step->execute();

        $this->assertFalse($result->success);
        $this->assertStringContainsString('Pfad existiert nicht', $result->message);
    }
}
