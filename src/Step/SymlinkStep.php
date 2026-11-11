<?php
declare(strict_types=1);

namespace SetupRunner\Step;

use SetupRunner\StepResult;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class SymlinkStep implements StepInterface
{
    public function getName(): string
    {
        return 'symlink';
    }

    public function getDescription(): string
    {
        return 'Erstellt einen symbolischen Link (Symlink)';
    }

    public function __construct(
        private readonly string $origin,
        private readonly string $target,
        private readonly bool $copyOnWindows = false,
        private readonly Filesystem $filesystem = new Filesystem()
    ) {}

    public function execute(): StepResult
    {
        try {
            $this->filesystem->symlink($this->origin, $this->target, $this->copyOnWindows);

            return new StepResult(true, "Symlink erfolgreich erstellt: '{$this->target}' -> '{$this->origin}'");
        } catch (IOExceptionInterface|\Throwable $e) {
            return new StepResult(false, "Fehler beim Erstellen des Symlinks '{$this->target}': " . $e->getMessage());
        }
    }
}
