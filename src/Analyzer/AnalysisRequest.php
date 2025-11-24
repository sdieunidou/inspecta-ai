<?php

declare(strict_types=1);

namespace InspectaAi\Analyzer;

use InspectaAi\Runner\Context\RunnerContext;

final class AnalysisRequest
{
    public function __construct(
        public readonly string $prompt,
        public readonly string $content,
        public readonly RunnerContext $context,
    ) {
    }
}
