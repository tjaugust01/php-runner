<?php
declare(strict_types=1);

namespace SetupRunner\Step;

use SetupRunner\StepResult;
use Symfony\Component\Process\Process;

class RunStep implements StepInterface
{
    public function getName(): string
    {
        return 'run';
    }

    public function getDescription(): string
    {
        return 'Führt einen Konsolenbefehl aus';
    }

    public function __construct(
        private readonly string $command,
        private readonly string|array|null $input = null,
        private readonly bool $passthrough = false,
        private readonly ?float $timeout = 300.0
    ) {}

    public function execute(): StepResult
    {
        $process = Process::fromShellCommandline($this->command);
        $process->setTimeout($this->timeout);

        if ($this->passthrough) {
            try {
                if (Process::isTtySupported()) {
                    $process->setTty(true);
                } else {
                    $process->setPty(true);
                }
                $process->run();
            } catch (\Throwable) {
                $process->run();
            }

            if ($process->isSuccessful()) {
                return new StepResult(true, "Interaktiver Befehl erfolgreich ausgeführt: '{$this->command}'");
            }

            return new StepResult(false, "Interaktiver Befehl '{$this->command}' fehlgeschlagen mit Exit-Code {$process->getExitCode()}");
        }

        if ($this->input !== null) {
            $inputString = is_array($this->input)
                ? implode("\n", $this->input) . "\n"
                : $this->input;

            $process->setInput($inputString);
        }

        $process->run();

        $output = trim($process->getOutput() . ($process->getErrorOutput() ? "\n" . $process->getErrorOutput() : ''));

        if ($process->isSuccessful()) {
            $msg = "Befehl erfolgreich ausgeführt: '{$this->command}'";
            if ($output !== '') {
                $msg .= " (Ausgabe: {$output})";
            }
            return new StepResult(true, $msg);
        }

        return new StepResult(
            false,
            "Befehl '{$this->command}' fehlgeschlagen mit Exit-Code {$process->getExitCode()}" . ($output !== '' ? ": {$output}" : "")
        );
    }
}