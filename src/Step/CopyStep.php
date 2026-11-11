<?php
declare(strict_types=1);

namespace SetupRunner\Step;

use SetupRunner\StepResult;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class CopyStep implements StepInterface
{
    public function getName(): string
    {
        return "copy";
    }

    public function getDescription(): string
    {
        return "Kopiert eine Datei oder ein Verzeichnis";
    }

    public function __construct(
        private readonly string $sourcePath,
        private readonly string $targetPath,
        private readonly bool $skipIfExists = true,
        private readonly Filesystem $filesystem = new Filesystem()
    ) {}

    public function execute(): StepResult
    {
        if (!$this->filesystem->exists($this->sourcePath)) {
            return new StepResult(false, "Quellpfad existiert nicht: {$this->sourcePath}");
        }

        if ($this->skipIfExists && $this->filesystem->exists($this->targetPath)) {
            return new StepResult(true, "Zielpfad existiert bereits, Kopieren übersprungen: {$this->targetPath}");
        }

        try {
            if (is_dir($this->sourcePath)) {
                $this->filesystem->mirror($this->sourcePath, $this->targetPath, null, ['override' => true]);
            } else {
                $this->filesystem->copy($this->sourcePath, $this->targetPath, true);
            }

            return new StepResult(true, "Erfolgreich kopiert: '{$this->sourcePath}' -> '{$this->targetPath}'");
        } catch (IOExceptionInterface|\Throwable $e) {
            return new StepResult(false, "Fehler beim Kopieren von '{$this->sourcePath}' nach '{$this->targetPath}': " . $e->getMessage());
        }
    }
}