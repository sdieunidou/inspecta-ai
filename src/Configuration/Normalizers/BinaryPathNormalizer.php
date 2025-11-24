<?php

declare(strict_types=1);

namespace InspectaAi\Configuration\Normalizers;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;

final class BinaryPathNormalizer
{
    public static function normalize(Options $options, string $binaryPath): string
    {
        if (!is_file($binaryPath)) {
            throw new InvalidOptionsException(\sprintf(
                'Binary "%s" cannot be found.',
                $binaryPath
            ));
        }

        return $binaryPath;
    }
}
