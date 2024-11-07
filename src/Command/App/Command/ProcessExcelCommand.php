<?php

namespace App\Command\App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'App\Command\ProcessExcelCommand',
    description: 'Add a short description for your command',
)]
class ProcessExcelCommand extends Command
{
    protected static $defaultName = 'app:process-excel';
    protected function configure(): void
    {
        $this
            ->setDescription('Process an Excel file and insert data into the database')
            ->addArgument('file', InputArgument::REQUIRED, 'The path to the Excel file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
