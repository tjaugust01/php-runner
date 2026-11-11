<?php
declare(strict_types=1);

namespace SetupRunner\Step;

use SetupRunner\StepResult;
use Symfony\Component\Filesystem\Filesystem;

class ExistsStep implements StepInterface
{
    public function getName(): string
    {
        return 'exists';
    }

    public function getDescription(): string
    {
        return 'Prüft, ob eine Datei oder ein Verzeichnis existiert';
    }

    public function __construct(
        private readonly string $path,
        private readonly bool $expectExists = true,
        private readonly Filesystem $filesystem = new Filesystem()
    ) {}

    public function execute(): StepResult
    {
        $exists = $this->filesystem->exists($this->path);

        if ($exists === $this->expectExists) {
            $msg = $exists
                ? "Pfad existiert wie erwartet: {$this->path}"
                : "Pfad existiert nicht (wie erwartet): {$this->path}";
            return new StepResult(true, $msg);
        }

        $msg = $this->expectExists
            ? "Existenz-Prüfung fehlgeschlagen: Pfad '{$this->path}' existiert nicht."
            : "Existenz-Prüfung fehlgeschlagen: Pfad '{$this->path}' existiert bereits.";

        return new StepResult(false, $msg);
    }
}
