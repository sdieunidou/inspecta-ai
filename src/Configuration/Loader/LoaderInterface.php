<?php

declare(strict_types=1);

namespace InspectaAi\Configuration\Loader;

interface LoaderInterface
{
    public function load(): array;
}
