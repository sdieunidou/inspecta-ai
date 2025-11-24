<?php

declare(strict_types=1);

namespace InspectaAi\Configuration\Normalizers;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;

final class TimeoutNormalizer
{
    public static function normalize(Options $options, int $timeout): int
    {
        if ($timeout < 0) {
            throw new InvalidOptionsException('Timeout must be a positive integer.');
        }

        return $timeout;
    }
}
