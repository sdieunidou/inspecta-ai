<?php

declare(strict_types=1);

namespace InspectaAi\Configuration\Normalizers;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;

final class PromptRunnerNormalizer
{
    public static function normalize(Options $options, array $prompts): array
    {
        $availableRunners = array_keys($options['runners'] ?? []);

        foreach ($prompts as $name => $promptConfig) {
            $runner = $promptConfig['runner'] ?? null;
            if (!\in_array($runner, $availableRunners, true)) {
                throw new InvalidOptionsException(\sprintf(
                    'Prompt "%s" references an unknown runner "%s". Available runners: %s',
                    (string) $name,
                    (string) $runner,
                    implode(', ', $availableRunners)
                ));
            }
        }

        return $prompts;
    }
}
