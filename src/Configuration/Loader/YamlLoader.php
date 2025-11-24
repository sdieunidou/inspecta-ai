<?php

declare(strict_types=1);

namespace InspectaAi\Configuration\Loader;

use Symfony\Component\Yaml\Yaml;

class YamlLoader implements LoaderInterface
{
    public function __construct(private string $configPath)
    {
    }

    public function load(): array
    {
        return Yaml::parseFile($this->configPath) ?? [];
    }
}
