<?php

declare(strict_types=1);

namespace InspectaAi\Analyzer\Prompt;

use InspectaAi\Runner\Context\RunnerContext;

final class PromptPreprocessor
{
    public function process(string $template, RunnerContext $context): string
    {
        return str_replace('%%file%%', $context->getFilepath(), subject: $template);
    }
}
