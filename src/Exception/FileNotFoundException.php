<?php

declare(strict_types=1);

namespace InspectaAi\Exception;

class FileNotFoundException extends \RuntimeException
{
    public static function forFile(string $filepath): self
    {
        return new self(\sprintf('File "%s" not found', $filepath));
    }
}
