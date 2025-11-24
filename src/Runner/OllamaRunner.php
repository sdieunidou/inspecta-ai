<?php

declare(strict_types=1);

namespace InspectaAi\Runner;

use InspectaAi\Analyzer\Request\AnalysisRequest;
use InspectaAi\Exception\RunnerExecutionException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class OllamaRunner implements RunnerInterface
{
    public function analyze(AnalysisRequest $request): string
    {
        $runnerConfig = $request->context->getRunnerConfig();

        $process = new Process([
            $runnerConfig['binary'],
            'run',
            $runnerConfig['model'],
            $request->prompt,
            $request->content,
        ]);

        $process->setTimeout($runnerConfig['timeout']);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            throw RunnerExecutionException::fromProcess(
                $request->context->getRunnerName(),
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
