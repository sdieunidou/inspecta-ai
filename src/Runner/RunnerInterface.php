<?php

declare(strict_types=1);

namespace InspectaAi\Runner;

use InspectaAi\Analyzer\Request\AnalysisRequest;

interface RunnerInterface
{
    public function analyze(AnalysisRequest $request): string;

    public function supports(string $runnerType): bool;
}
