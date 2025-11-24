<?php

declare(strict_types=1);

namespace InspectaAi\Analyzer;

use InspectaAi\Analyzer\Loader\FileLoaderInterface;
use InspectaAi\Analyzer\Prompt\PromptPreprocessor;
use InspectaAi\Runner\Context\RunnerContextFactory;

final class AnalysisRequestFactory
{
    public function __construct(
        private RunnerContextFactory $contextFactory,
        private FileLoaderInterface $fileLoader,
        private PromptPreprocessor $promptPreprocessor,
    ) {
    }

    public function create(string $prompt, string $file): AnalysisRequest
    {
        $context = $this->contextFactory->create($prompt, $file);

        $promptTemplate = $this->fileLoader->load($context->getPromptConfig()['template']);
        $processedPrompt = $this->promptPreprocessor->process($promptTemplate, $context);

        $content = $this->fileLoader->load($file);

        return new AnalysisRequest($processedPrompt, $content, $context);
    }
}
