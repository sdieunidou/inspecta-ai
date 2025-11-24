<?php

declare(strict_types=1);

namespace InspectaAi\Console\Command;

use InspectaAi\Analyzer\AnalyzerFactory;
use InspectaAi\Configuration\Loader\YamlLoader;
use InspectaAi\Runner\OllamaRunner;
use InspectaAi\Runner\RunnerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'analyze', description: 'Run AI analysis on a target file using a specific prompt')]
class AnalyzeCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('prompt', InputArgument::REQUIRED, 'The prompt configuration to use');
        $this->addArgument('file', InputArgument::REQUIRED, 'The file to analyze');
        $this->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'The configuration file to use', 'inspecta-ai.yaml');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configLoader = new YamlLoader($input->getOption('config'));
        $runnerRegistry = new RunnerRegistry([
            new OllamaRunner(),
        ]);

        $analyzerFactory = new AnalyzerFactory($configLoader, $runnerRegistry);
        $analyzer = $analyzerFactory->create();

        $result = $analyzer->analyze(
            $input->getArgument('prompt'),
            $input->getArgument('file'),
        );

        $output->writeln($result->getRawResult());

        return Command::SUCCESS;
    }
}
