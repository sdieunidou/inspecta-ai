<?php

declare(strict_types=1);

namespace InspectaAi\Runner\Context;

use InspectaAi\Configuration\Configuration;
use InspectaAi\Runner\RunnerRegistry;

final class RunnerContextFactory
{
    public function __construct(
        private Configuration $configuration,
        private RunnerRegistry $runnerRegistry,
    ) {
    }

    public function create(string $promptName, string $filepath): RunnerContext
    {
        $promptConfig = $this->configuration->getPromptConfig($promptName);
        $runnerConfig = $this->configuration->getRunnerConfig($promptConfig['runner']);
        $runner = $this->runnerRegistry->get($runnerConfig['type']);

        return new RunnerContext(
            $promptName,
            $promptConfig,
            $promptConfig['runner'],
            $runnerConfig,
            $runner,
            $filepath,
        );
    }
}
