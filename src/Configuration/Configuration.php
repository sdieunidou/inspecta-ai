<?php

declare(strict_types=1);

namespace InspectaAi\Configuration;

use Closure;
use InspectaAi\Configuration\Loader\LoaderInterface;
use InspectaAi\Configuration\Normalizers\BinaryPathNormalizer;
use InspectaAi\Configuration\Normalizers\PromptProviderNormalizer;
use InspectaAi\Configuration\Normalizers\TemplatePathNormalizer;
use InspectaAi\Configuration\Normalizers\TimeoutNormalizer;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
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
    private function resolveConfig(): void
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired([
            'providers',
            'prompts',
        ]);

        $resolver->setOptions('providers', function (OptionsResolver $providerResolver): void {
            $providerResolver
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
                ->setRequired(['template', 'provider'])
                ->setAllowedTypes('template', 'string')
                ->setAllowedTypes('provider', 'string')
                ->setNormalizer('template', Closure::fromCallable([TemplatePathNormalizer::class, 'normalize']))
            ;
        });

        $resolver->setNormalizer('prompts', Closure::fromCallable([PromptProviderNormalizer::class, 'normalize']));

        $this->config = $resolver->resolve($this->loader->load());
    }
}
