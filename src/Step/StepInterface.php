<?php
declare(strict_types=1);

namespace SetupRunner\Step;

use SetupRunner\StepResult;

interface StepInterface {
    public function getName(): string;
    public function getDescription(): string;
    public function execute(): StepResult;
}