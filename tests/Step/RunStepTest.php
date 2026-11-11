<?php
declare(strict_types=1);

namespace SetupRunner\Tests\Step;

use PHPUnit\Framework\TestCase;
use SetupRunner\Step\RunStep;

class RunStepTest extends TestCase
{
    public function testRunStepSuccess(): void
    {
        $step = new RunStep('echo "Hello World"');
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertStringContainsString('Befehl erfolgreich ausgeführt', $result->message);
        $this->assertStringContainsString('Hello World', $result->message);
    }

    public function testRunStepFailure(): void
    {
        $step = new RunStep('exit 1');
        $result = $step->execute();

        $this->assertFalse($result->success);
        $this->assertStringContainsString('fehlgeschlagen mit Exit-Code 1', $result->message);
    }

    public function testRunStepWithInputString(): void
    {
        $step = new RunStep('read NAME; echo "Hallo $NAME"', input: "Alice\n");
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertStringContainsString('Hallo Alice', $result->message);
    }

    public function testRunStepWithInputArray(): void
    {
        $step = new RunStep('read FIRST; read LAST; echo "$FIRST $LAST"', input: ["John", "Doe"]);
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertStringContainsString('John Doe', $result->message);
    }

    public function testRunStepWithPassthrough(): void
    {
        $step = new RunStep('true', passthrough: true);
        $result = $step->execute();

        $this->assertTrue($result->success);
        $this->assertStringContainsString('Interaktiver Befehl erfolgreich ausgeführt', $result->message);
    }
}
