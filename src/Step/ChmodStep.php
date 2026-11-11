<?php
declare(strict_types=1);

namespace SetupRunner\Step;

use SetupRunner\StepResult;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class ChmodStep implements StepInterface
{
    public function getName(): string
    {
        return 'chmod';
    }

    public function getDescription(): string
    {
        return 'Ändert die Zugriffsrechte (chmod) einer Datei oder eines Verzeichnisses';
    }

    public function __construct(
        private readonly string $path,
        private readonly int|string $mode = 0755,
        private readonly bool $recursive = false,
        private readonly Filesystem $filesystem = new Filesystem()
    ) {}

    public function execute(): StepResult
    {
        if (!$this->filesystem->exists($this->path)) {
            return new StepResult(false, "Pfad existiert nicht: {$this->path}");
        }

        try {
            $intMode = is_string($this->mode) ? octdec($this->mode) : $this->mode;
            $this->filesystem->chmod($this->path, $intMode, 0000, $this->recursive);

            $modeFormatted = '0' . decoct($intMode);
            return new StepResult(true, "Zugriffsrechte (chmod {$modeFormatted}) erfolgreich gesetzt für: {$this->path}");
        } catch (IOExceptionInterface|\Throwable $e) {
            return new StepResult(false, "Fehler beim Setzen von chmod für '{$this->path}': " . $e->getMessage());
        }
    }
}
