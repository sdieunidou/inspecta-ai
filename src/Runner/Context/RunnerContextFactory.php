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
        return RunnerContext::fromPrompt(
            $promptName,
            $this->configuration,
            $this->runnerRegistry,
            $filepath,
        );
    }
}
