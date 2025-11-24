<?php

declare(strict_types=1);

namespace InspectaAi\Configuration\Normalizers;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;

final class PromptProviderNormalizer
{
    public static function normalize(Options $options, array $prompts): array
    {
        $availableProviders = array_keys($options['providers'] ?? []);

        foreach ($prompts as $name => $promptConfig) {
            $provider = $promptConfig['provider'] ?? null;
            if (!\in_array($provider, $availableProviders, true)) {
                throw new InvalidOptionsException(\sprintf(
                    'Prompt "%s" references an unknown provider "%s". Available providers: %s',
                    (string) $name,
                    (string) $provider,
                    implode(', ', $availableProviders)
                ));
            }
        }

        return $prompts;
    }
}
