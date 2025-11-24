<?php

declare(strict_types=1);

namespace InspectaAi\Analyzer\Loader;

use InspectaAi\Exception\FileNotFoundException;
use InspectaAi\Exception\FileNotReadableException;

class FileLoader implements FileLoaderInterface
{
    public function load(string $file): string
    {
        if (!is_file($file)) {
            throw FileNotFoundException::forFile($file);
        }

        if (!is_readable($file)) {
            throw FileNotReadableException::forFile($file);
        }

        $content = @file_get_contents($file);
        if ($content === false) {
            throw new \RuntimeException(\sprintf('Failed to read file "%s"', $file));
        }

        return $content;
    }
}
