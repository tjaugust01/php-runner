<?php
declare(strict_types=1);

namespace SetupRunner\Tests\Step;

use PHPUnit\Framework\TestCase;
use SetupRunner\Step\CheckStep;

class CheckStepTest extends TestCase
{
    public function testCheckStepSuccess(): void
    {
        $step = new CheckStep('php -v', 'PHP');
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertStringContainsString('Check erfolgreich', $result->message);
    }

    public function testCheckStepFailureShowsActualOutput(): void
    {
        $step = new CheckStep('echo "foo"', 'bar');
        $result = $step->execute();

        $this->assertFalse($result->success);
        $this->assertStringContainsString('Check fehlgeschlagen', $result->message);
        $this->assertStringContainsString('erhalten: \'foo\'', $result->message);
    }
}
