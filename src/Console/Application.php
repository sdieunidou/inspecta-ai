<?php

declare(strict_types=1);

namespace InspectaAi\Console;

use InspectaAi\Console\Command\AnalyzeCommand;
use Symfony\Component\Console\Application as BaseApplication;

final class Application extends BaseApplication
{
    public const NAME = 'Inspecta AI';
    public const VERSION = '0.0.5-DEV';

    public function __construct()
    {
        parent::__construct(self::NAME, self::VERSION);

        $this->add(new AnalyzeCommand());
    }
}
