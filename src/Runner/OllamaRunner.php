<?php

declare(strict_types=1);

namespace InspectaAi\Runner;

use InspectaAi\Exception\RunnerExecutionException;
use InspectaAi\Runner\Context\RunnerContext;
use Symfony\Component\Process\Exception\ProcessFailedException;
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

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            throw RunnerExecutionException::fromProcess(
                $context->getRunnerName(),
                $e->getMessage()
            );
        }

        return $process->getOutput();
    }

    public function supports(string $runnerType): bool
    {
        return $runnerType === 'ollama';
    }
}
