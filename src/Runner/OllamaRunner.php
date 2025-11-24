<?php

declare(strict_types=1);

namespace InspectaAi\Runner;

use InspectaAi\Runner\Context\RunnerContext;
use Symfony\Component\Process\Process;

class OllamaRunner implements RunnerInterface
{
    public function analyze(string $prompt, string $content, RunnerContext $context): string
    {
        $runnerConfig = $context->getRunnerConfig();

        $process = new Process([
            $runnerConfig['binary'],
            'run',
            $runnerConfig['model'],
            $prompt,
            $content,
        ]);

        $process->setTimeout($runnerConfig['timeout']);
        $process->mustRun();

        return $process->getOutput();
    }

    public function supports(string $runnerType): bool
    {
        return $runnerType === 'ollama';
    }
}
