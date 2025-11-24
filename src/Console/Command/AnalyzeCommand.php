<?php

declare(strict_types=1);

namespace InspectaAi\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'analyze', description: 'Run configurable AI analysis on a target file')]
class AnalyzeCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Analyzing...');
        return Command::SUCCESS;
    }
}
