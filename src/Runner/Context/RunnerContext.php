<?php

declare(strict_types=1);

namespace InspectaAi\Runner\Context;

use InspectaAi\Configuration\Configuration;
use InspectaAi\Runner\RunnerInterface;
use InspectaAi\Runner\RunnerRegistry;

final class RunnerContext
{
    /**
     * @param array<string, mixed> $promptConfig
     * @param array<string, mixed> $runnerConfig
     */
    private function __construct(
        private string $promptName,
        private array $promptConfig,
        private string $runnerName,
        private array $runnerConfig,
        private RunnerInterface $runner,
        private string $filepath,
    ) {
    }

    public static function fromPrompt(
        string $promptName,
        Configuration $configuration,
        RunnerRegistry $runnerRegistry,
        string $filepath,
    ): self {
        $promptConfig = $configuration->getPromptConfig($promptName);
        $runnerConfig = $configuration->getRunnerConfig($promptConfig['runner']);
        $runner = $runnerRegistry->get($runnerConfig['type']);

        return new self(
            $promptName,
            $promptConfig,
            $promptConfig['runner'],
            $runnerConfig,
            $runner,
            $filepath,
        );
    }

    public function getPromptName(): string
    {
        return $this->promptName;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPromptConfig(): array
    {
        return $this->promptConfig;
    }

    public function getRunnerName(): string
    {
        return $this->runnerName;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRunnerConfig(): array
    {
        return $this->runnerConfig;
    }

    public function getRunner(): RunnerInterface
    {
        return $this->runner;
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }
}
