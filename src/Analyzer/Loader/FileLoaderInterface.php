<?php

declare(strict_types=1);

namespace InspectaAi\Analyzer\Loader;

interface FileLoaderInterface
{
    public function load(string $file): string;
}
