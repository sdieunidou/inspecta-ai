<?php

declare(strict_types=1);

namespace InspectaAi\Analyzer;

use InspectaAi\Analyzer\Loader\FileLoader;
use InspectaAi\Analyzer\Loader\FileLoaderInterface;
use InspectaAi\Analyzer\Prompt\PromptPreprocessor;
use InspectaAi\Configuration\Configuration;
use InspectaAi\Configuration\Loader\LoaderInterface;
use InspectaAi\Runner\Context\RunnerContextFactory;
use InspectaAi\Runner\RunnerRegistry;

final class AnalyzerFactory
{
    public function __construct(
        private LoaderInterface $configLoader,
        private RunnerRegistry $runnerRegistry,
        private ?FileLoaderInterface $fileLoader = null,
    ) {
        $this->fileLoader = $fileLoader ?? new FileLoader();
    }

    public function create(): Analyzer
    {
        $configuration = new Configuration($this->configLoader);

        $contextFactory = new RunnerContextFactory($configuration, $this->runnerRegistry);
        $promptPreprocessor = new PromptPreprocessor();

        $requestFactory = new AnalysisRequestFactory(
            $contextFactory,
            $this->fileLoader,
            $promptPreprocessor,
        );

        return new Analyzer($requestFactory);
    }
}
