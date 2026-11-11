<?php
declare(strict_types=1);

namespace SetupRunner\Step;

use SetupRunner\StepResult;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class AppendToFileStep implements StepInterface
{
    public function getName(): string
    {
        return 'appendToFile';
    }

    public function getDescription(): string
    {
        return 'Hängt Inhalt an eine Datei an';
    }

    public function __construct(
        private readonly string $targetPath,
        private readonly string $content,
        private readonly bool $lock = false,
        private readonly Filesystem $filesystem = new Filesystem()
    ) {}

    public function execute(): StepResult
    {
        try {
            $this->filesystem->appendToFile($this->targetPath, $this->content, $this->lock);

            return new StepResult(true, "Inhalt erfolgreich an '{$this->targetPath}' angehängt.");
        } catch (IOExceptionInterface|\Throwable $e) {
            return new StepResult(false, "Fehler beim Anhängen an Datei '{$this->targetPath}': " . $e->getMessage());
        }
    }
}
