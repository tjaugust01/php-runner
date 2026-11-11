<?php
declare(strict_types=1);

namespace SetupRunner;

use SetupRunner\Config\ConfigLoader;
use SetupRunner\StepFactory;

class Runner {
    public function __construct(
        private ConfigLoader $loader,
        private StepFactory $factory
    ) {}

    /**
     * @param string $configPath
     * @param callable(\SetupRunner\Step\StepInterface, StepResult): void|null $onStepExecuted
     * @return StepResult[]
     */
    public function run(string $configPath, ?callable $onStepExecuted = null): array {
        $config = $this->loader->load($configPath);
        $results = [];

        foreach ($config['steps'] as $stepConfig) {
            $step = $this->factory->create($stepConfig);
            $result = $step->execute();
            $results[] = $result;

            if ($onStepExecuted !== null) {
                $onStepExecuted($step, $result);
            }
        }

        return $results;
    }
}