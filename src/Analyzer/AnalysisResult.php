<?php

declare(strict_types=1);

namespace InspectaAi\Analyzer;

final class AnalysisResult
{
    private string $rawResult;

    public function __construct(string $rawResult)
    {
        $this->setRawResult($rawResult);
    }

    private function setRawResult(string $rawResult): void
    {
        $this->rawResult = trim($rawResult);
    }
    public function getRawResult(): string
    {
        return $this->rawResult;
    }

    public function isJson(): bool
    {
        return json_validate($this->rawResult);
    }

    /**
     * @return array<string, mixed>
     */
    public function getJson(): array
    {
        if (!$this->isJson()) {
            throw new \RuntimeException('Analysis result is not a valid JSON string.');
        }

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($this->rawResult, true, flags: JSON_THROW_ON_ERROR);

        return $decoded;
    }
}
