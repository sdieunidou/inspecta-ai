<?php

declare(strict_types=1);

namespace InspectaAi\Analyzer;

class FileLoader
{
    public function load(string $file): string
    {
        if (!is_file($file)) {
            throw new \InvalidArgumentException(\sprintf('File "%s" not found', $file));
        }

        if (!is_readable($file)) {
            throw new \InvalidArgumentException(\sprintf('File "%s" is not readable', $file));
        }

        if (false === $content = file_get_contents($file)) {
            throw new \InvalidArgumentException(\sprintf('Failed to read file "%s"', $file));
        }

        return $content;
    }
}
