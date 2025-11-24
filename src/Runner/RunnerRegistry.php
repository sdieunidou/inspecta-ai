<?php

declare(strict_types=1);

namespace InspectaAi\Runner;

final class RunnerRegistry
{
    /** @var RunnerInterface[] */
    private array $runners = [];

    /**
     * @param iterable<RunnerInterface> $runners
     */
    public function __construct(iterable $runners)
    {
        foreach ($runners as $runner) {
            $this->runners[] = $runner;
        }
    }

    public function get(string $runnerType): RunnerInterface
    {
        foreach ($this->runners as $runner) {
            if ($runner->supports($runnerType)) {
                return $runner;
            }
        }

        throw new \InvalidArgumentException(\sprintf('Runner "%s" is not registered.', $runnerType));
    }

    /**
     * @return RunnerInterface[]
     */
    public function all(): array
    {
        return $this->runners;
    }
}
