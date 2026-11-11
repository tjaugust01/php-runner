<?php
declare(strict_types=1);
namespace SetupRunner;

use SetupRunner\Step\AppendToFileStep;
use SetupRunner\Step\CheckStep;
use SetupRunner\Step\ChmodStep;
use SetupRunner\Step\CleanDirStep;
use SetupRunner\Step\CopyStep;
use SetupRunner\Step\CreateFileStep;
use SetupRunner\Step\ExistsStep;
use SetupRunner\Step\MakeDirStep;
use SetupRunner\Step\RunStep;
use SetupRunner\Step\StepInterface;
use SetupRunner\Step\SymlinkStep;

class StepFactory {
    public function create(array $stepConfig): StepInterface
    {
        return match (strtolower($stepConfig['type'])) {
            'run' => new RunStep(
                $stepConfig['command'],
                $stepConfig['input'] ?? null,
                $stepConfig['passthrough'] ?? $stepConfig['tty'] ?? $stepConfig['interactive'] ?? false
            ),
            'check' => new CheckStep($stepConfig['command'], $stepConfig['expect_contains']),
            'copy' => new CopyStep(
                $stepConfig['source'],
                $stepConfig['destination'],
                $stepConfig['skip_if_exists'] ?? true
            ),
            'createfile' => new CreateFileStep(
                $stepConfig['path'],
                $stepConfig['content'] ?? '',
                $stepConfig['skip_if_exists'] ?? true
            ),
            'cleandir' => new CleanDirStep($stepConfig['path']),
            'makedir' => new MakeDirStep(
                $stepConfig['path'],
                $stepConfig['mode'] ?? 0777
            ),
            'exists' => new ExistsStep(
                $stepConfig['path'],
                $stepConfig['expect_exists'] ?? true
            ),
            'chmod' => new ChmodStep(
                $stepConfig['path'],
                $stepConfig['mode'] ?? 0755,
                $stepConfig['recursive'] ?? false
            ),
            'symlink' => new SymlinkStep(
                $stepConfig['origin'] ?? $stepConfig['source'],
                $stepConfig['target'] ?? $stepConfig['destination'],
                $stepConfig['copy_on_windows'] ?? false
            ),
            'appendtofile' => new AppendToFileStep(
                $stepConfig['path'] ?? $stepConfig['target'],
                $stepConfig['content'] ?? '',
                $stepConfig['lock'] ?? false
            ),
            default => throw new \InvalidArgumentException("Unbekannter Step-Typ: {$stepConfig['type']}"),
        };
    }
}