<?php

declare(strict_types=1);

namespace InspectaAi\Analyzer;

use InspectaAi\Analyzer\AnalysisResult;

class Analyzer
{
    public function __construct(
        private AnalysisOrchestrator $orchestrator,
    ) {
    }

    public function analyze(string $prompt, string $file): AnalysisResult
    {
        $request = $this->orchestrator->orchestrate($prompt, $file);

        $rawResult = $request->context->getRunner()->analyze(
            $request->prompt,
            $request->content,
            $request->context,
        );

        return new AnalysisResult($rawResult);
    }
}
