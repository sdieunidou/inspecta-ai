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

    public function create(string $promptName, string $filepath): AnalysisRequest
    {
        $context = $this->contextFactory->create($promptName, $filepath);

        $promptTemplate = $this->fileLoader->load($context->getPromptConfig()['template']);
        $processedPrompt = $this->promptPreprocessor->process($promptTemplate, $context);

        $content = $this->fileLoader->load($filepath);

        return new AnalysisRequest($processedPrompt, $content, $context);
    }
}
