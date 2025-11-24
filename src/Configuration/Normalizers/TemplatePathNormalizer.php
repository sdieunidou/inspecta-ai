<?php

declare(strict_types=1);

namespace InspectaAi\Configuration\Normalizers;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;

final class TemplatePathNormalizer
{
    public static function normalize(Options $options, string $templatePath): string
    {
        if (!is_file($templatePath)) {
            throw new InvalidOptionsException(\sprintf(
                'Template "%s" cannot be found.',
                $templatePath
            ));
        }

        return $templatePath;
    }
}
