<?php
declare(strict_types=1);

namespace SetupRunner\Step;

use SetupRunner\StepResult;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class MakeDirStep implements StepInterface
{
    public function getName(): string
    {
        return 'makeDir';
    }

    public function getDescription(): string
    {
        return 'Erstellt ein Verzeichnis';
    }

    public function __construct(
        private readonly string $path,
        private readonly int $mode = 0777,
        private readonly Filesystem $filesystem = new Filesystem()
    ) {}

    public function execute(): StepResult
    {
        if ($this->filesystem->exists($this->path)) {
            if (is_dir($this->path)) {
                return new StepResult(true, "Verzeichnis existiert bereits: {$this->path}");
            }
            return new StepResult(false, "Pfad existiert bereits, ist aber kein Verzeichnis: {$this->path}");
        }

        try {
            $this->filesystem->mkdir($this->path, $this->mode);

            return new StepResult(true, "Verzeichnis erfolgreich erstellt: {$this->path}");
        } catch (IOExceptionInterface|\Throwable $e) {
            return new StepResult(false, "Fehler beim Erstellen des Verzeichnisses '{$this->path}': " . $e->getMessage());
        }
    }
}