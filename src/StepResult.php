<?php
declare(strict_types=1);

namespace SetupRunner;

final class StepResult {
    public function __construct(
        public readonly bool $success,
        public readonly string $message
    ) {}
}