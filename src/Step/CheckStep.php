<?php
declare(strict_types=1);

namespace SetupRunner\Step;

use SetupRunner\StepResult;
use Symfony\Component\Process\Process;

class CheckStep implements StepInterface
{
    public function getName(): string
    {
        return 'check';
    }

    public function getDescription(): string
    {
        return 'Prüft, ob die Ausgabe eines Befehls eine erwartete Zeichenkette enthält';
    }

    public function __construct(
        private readonly string $command,
        private readonly string $expectedCondition,
    ) {}

    public function execute(): StepResult
    {
        try {
            $process = Process::fromShellCommandline($this->command);
            $process->run();

            $output = trim($process->getOutput() . ($process->getErrorOutput() ? "\n" . $process->getErrorOutput() : ''));

            if (str_contains($output, $this->expectedCondition)) {
                return new StepResult(true, "Check erfolgreich: '{$this->command}' enthält '{$this->expectedCondition}'");
            }

            $shortOutput = strlen($output) > 100 ? substr($output, 0, 100) . '...' : $output;

            return new StepResult(
                false,
                "Check fehlgeschlagen für '{$this->command}': Erwartet wurde '{$this->expectedCondition}', erhalten: '{$shortOutput}'"
            );
        } catch (\Throwable $e) {
            return new StepResult(false, "Befehl konnte nicht ausgeführt werden: '{$this->command}' ({$e->getMessage()})");
        }
    }
}