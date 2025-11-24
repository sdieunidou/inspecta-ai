<?php

declare(strict_types=1);

namespace InspectaAi\Analyzer;

use InspectaAi\Analyzer\AnalysisResult;
use InspectaAi\Analyzer\Loader\FileLoaderInterface;
use InspectaAi\Analyzer\Prompt\PromptPreprocessor;
use InspectaAi\Configuration\Configuration;
use InspectaAi\Runner\Context\RunnerContext;
use InspectaAi\Runner\RunnerRegistry;

class Analyzer
{
    public function __construct(
        private Configuration $configuration,
        private FileLoaderInterface $fileLoader,
        private RunnerRegistry $runnerRegistry,
    ) {
    }

    public function analyze(string $prompt, string $file): AnalysisResult
    {
        $context = RunnerContext::fromPrompt(
            $prompt,
            $this->configuration,
            $this->runnerRegistry,
            $file,
        );

        $promptTemplate = $this->fileLoader->load($context->getPromptConfig()['template']);
        $promptTemplate = PromptPreprocessor::process($promptTemplate, $context);

        $content = $this->fileLoader->load($file);

        $rawResult = $context->getRunner()->analyze($promptTemplate, $content, $context);

        return new AnalysisResult($rawResult);
    }
}
