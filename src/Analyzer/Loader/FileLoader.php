<?php

declare(strict_types=1);

namespace InspectaAi\Analyzer\Loader;

use InspectaAi\Exception\FileNotFoundException;
use InspectaAi\Exception\FileNotReadableException;

class FileLoader implements FileLoaderInterface
{
    public function load(string $filepath): string
    {
        if (!is_file($filepath)) {
            throw FileNotFoundException::forFile($filepath);
        }

        if (!is_readable($filepath)) {
            throw FileNotReadableException::forFile($filepath);
        }

        $content = file_get_contents($filepath);
        if ($content === false) {
            throw new \RuntimeException(\sprintf('Failed to read file "%s"', $filepath));
        }

        return $content;
    }
}
