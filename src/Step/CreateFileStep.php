<?php
declare(strict_types=1);

namespace SetupRunner\Step;

use SetupRunner\StepResult;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class CreateFileStep implements StepInterface
{
    public function getName(): string
    {
        return 'createFile';
    }

    public function getDescription(): string
    {
        return 'Erstellt eine Datei';
    }

    public function __construct(
        private readonly string $targetPath,
        private readonly string $content = '',
        private readonly bool $skipIfExists = true,
        private readonly Filesystem $filesystem = new Filesystem()
    ) {}

    public function execute(): StepResult
    {
        if ($this->skipIfExists && $this->filesystem->exists($this->targetPath)) {
            return new StepResult(true, "Datei existiert bereits, Erstellen übersprungen: {$this->targetPath}");
        }

        try {
            $this->filesystem->dumpFile($this->targetPath, $this->content);

            return new StepResult(true, "Datei erfolgreich erstellt: {$this->targetPath}");
        } catch (IOExceptionInterface|\Throwable $e) {
            return new StepResult(false, "Fehler beim Erstellen der Datei '{$this->targetPath}': " . $e->getMessage());
        }
    }
}