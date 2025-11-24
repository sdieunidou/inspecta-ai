<?php

declare(strict_types=1);

namespace InspectaAi\Console\Command;

use InspectaAi\Analyzer\Analyzer;
use InspectaAi\Analyzer\Loader\FileLoader;
use InspectaAi\Configuration\Configuration;
use InspectaAi\Configuration\Loader\YamlLoader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'analyze', description: 'Run configurable AI analysis on a target file')]
class AnalyzeCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('prompt', InputArgument::REQUIRED, 'The prompt key to use');
        $this->addArgument('file', InputArgument::REQUIRED, 'The file to analyze');
        $this->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'The configuration file', 'inspecta-ai.yaml');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configuration = new Configuration(new YamlLoader($input->getOption('config')));
        $analyzer = new Analyzer($configuration, new FileLoader());

        $analyzer->analyze(
            $input->getArgument('prompt'),
            $input->getArgument('file'),
        );

        return Command::SUCCESS;
    }
}
