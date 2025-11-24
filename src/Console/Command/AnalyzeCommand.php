<?php

declare(strict_types=1);

namespace InspectaAi\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'analyze', description: 'Run configurable AI analysis on a target file')]
class AnalyzeCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'The file to analyze');
        $this->addArgument('prompt', InputArgument::REQUIRED, 'The prompt to use');
        $this->addArgument('provider', InputArgument::REQUIRED, 'The provider to use (ex: Ollama, OpenAI)');
        $this->addArgument('model', InputArgument::REQUIRED, 'The model to use (ex: llama3.2, gpt-5.1)');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Analyzing...');
        return Command::SUCCESS;
    }
}
