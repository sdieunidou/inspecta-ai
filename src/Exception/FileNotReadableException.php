<?php

declare(strict_types=1);

namespace InspectaAi\Exception;

class FileNotReadableException extends \RuntimeException
{
    public static function forFile(string $filepath): self
    {
        return new self(\sprintf('File "%s" is not readable', $filepath));
    }
}
