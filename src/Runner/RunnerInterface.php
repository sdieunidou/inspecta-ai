<?php

declare(strict_types=1);

namespace InspectaAi\Runner;

use InspectaAi\Runner\Context\RunnerContext;

interface RunnerInterface
{
    public function analyze(string $prompt, string $content, RunnerContext $context): string;

    public function supports(string $runnerType): bool;
}
