<?php
declare(strict_types=1);

namespace SetupRunner\Tests\Step;

use PHPUnit\Framework\TestCase;
use SetupRunner\Step\SymlinkStep;
use Symfony\Component\Filesystem\Filesystem;

class SymlinkStepTest extends TestCase
{
    private string $testDir;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->testDir = sys_get_temp_dir() . '/php_runner_symlink_test_' . uniqid();
        $this->filesystem->mkdir($this->testDir);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->testDir);
    }

    public function testSymlinkCreation(): void
    {
        $origin = $this->testDir . '/origin.txt';
        $target = $this->testDir . '/symlink.txt';
        file_put_contents($origin, 'Hello Symlink');

        $step = new SymlinkStep($origin, $target);
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertTrue(is_link($target));
        $this->assertEquals('Hello Symlink', file_get_contents($target));
    }
}
