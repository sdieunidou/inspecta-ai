<?php

declare(strict_types=1);

namespace InspectaAi\Analyzer;

use InspectaAi\Analyzer\AnalysisResult;

class Analyzer
{
    public function __construct(
        private AnalysisRequestFactory $requestFactory,
    ) {
    }

    public function analyze(string $prompt, string $file): AnalysisResult
    {
        $request = $this->requestFactory->create($prompt, $file);

        $rawResult = $request->context->getRunner()->analyze($request);

        return new AnalysisResult($rawResult);
    }
}
