<?php
declare(strict_types=1);

namespace SetupRunner\Step;

use SetupRunner\StepResult;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class CleanDirStep implements StepInterface
{
    public function getName(): string
    {
        return "cleanDir";
    }

    public function getDescription(): string
    {
        return "Bereinigt ein Verzeichnis (löscht alle Inhalte)";
    }

    public function __construct(
        private readonly string $path,
        private readonly Filesystem $filesystem = new Filesystem()
    ) {}

    public function execute(): StepResult
    {
        if (!$this->filesystem->exists($this->path) || !is_dir($this->path)) {
            return new StepResult(true, "Verzeichnis existiert nicht, nichts zu bereinigen: {$this->path}");
        }

        try {
            $files = array_diff(scandir($this->path), ['.', '..']);
            foreach ($files as $file) {
                $this->filesystem->remove($this->path . DIRECTORY_SEPARATOR . $file);
            }

            return new StepResult(true, "Verzeichnis bereinigt: {$this->path}");
        } catch (IOExceptionInterface|\Throwable $e) {
            return new StepResult(false, "Fehler beim Bereinigen von '{$this->path}': " . $e->getMessage());
        }
    }
}