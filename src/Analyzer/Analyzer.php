<?php

declare(strict_types=1);

namespace InspectaAi\Analyzer;

use InspectaAi\Configuration\Configuration;

class Analyzer
{
    public function __construct(
        private Configuration $configuration,
        private FileLoader $fileLoader = new FileLoader(),
    ) {
    }

    public function analyze(string $prompt, string $file): void
    {
        $this->checkIfPromptExists($prompt);

        $content = $this->fileLoader->load($file);
    }

    private function checkIfPromptExists(string $prompt): void
    {
        if (!isset($this->configuration->getConfig()['prompts'][$prompt])) {
            throw new \InvalidArgumentException(\sprintf('Prompt "%s" not found', $prompt));
        }
    }
}
