<?php

declare(strict_types=1);

namespace InspectaAi\Exception;

class RunnerExecutionException extends \RuntimeException
{
    public static function fromProcess(string $runnerType, string $error): self
    {
        return new self(\sprintf(
            'Runner "%s" execution failed: %s',
            $runnerType,
            $error
        ));
    }
}
