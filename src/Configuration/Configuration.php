<?php

declare(strict_types=1);

namespace InspectaAi\Configuration;

use Closure;
use InspectaAi\Configuration\Loader\LoaderInterface;
use InspectaAi\Configuration\Normalizers\BinaryPathNormalizer;
use InspectaAi\Configuration\Normalizers\PromptRunnerNormalizer;
use InspectaAi\Configuration\Normalizers\TemplatePathNormalizer;
use InspectaAi\Configuration\Normalizers\TimeoutNormalizer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Configuration
{
    private array $config = [];

    public function __construct(private LoaderInterface $loader)
    {
        $this->resolveConfig();
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPromptConfig(string $promptName): array
    {
        $prompts = $this->config['prompts'] ?? [];

        if (!isset($prompts[$promptName])) {
            throw new \InvalidArgumentException(\sprintf('Prompt "%s" not found', $promptName));
        }

        return $prompts[$promptName];
    }

    /**
     * @return array<string, mixed>
     */
    public function getRunnerConfig(string $runnerName): array
    {
        $runners = $this->config['runners'] ?? [];

        if (!isset($runners[$runnerName])) {
            throw new \InvalidArgumentException(\sprintf('Runner "%s" not found in configuration.', $runnerName));
        }

        return $runners[$runnerName];
    }

    private function resolveConfig(): void
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired([
            'runners',
            'prompts',
        ]);

        $resolver->setOptions('runners', function (OptionsResolver $runnerResolver): void {
            $runnerResolver
                ->setPrototype(true)
                ->setRequired(['type', 'binary', 'model', 'timeout'])
                ->setAllowedValues('type', ['ollama'])
                ->setAllowedTypes('type', 'string')
                ->setAllowedTypes('binary', 'string')
                ->setAllowedTypes('model', 'string')
                ->setAllowedTypes('timeout', 'int')
                ->setNormalizer('timeout', Closure::fromCallable([TimeoutNormalizer::class, 'normalize']))
                ->setNormalizer('binary', Closure::fromCallable([BinaryPathNormalizer::class, 'normalize']))
            ;
        });

        $resolver->setOptions('prompts', function (OptionsResolver $promptResolver): void {
            $promptResolver
                ->setPrototype(true)
                ->setRequired(['template', 'runner'])
                ->setAllowedTypes('template', 'string')
                ->setAllowedTypes('runner', 'string')
                ->setNormalizer('template', Closure::fromCallable([TemplatePathNormalizer::class, 'normalize']))
            ;
        });

        $resolver->setNormalizer('prompts', Closure::fromCallable([PromptRunnerNormalizer::class, 'normalize']));

        $this->config = $resolver->resolve($this->loader->load());
    }
}
