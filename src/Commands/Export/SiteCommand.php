<?php

namespace DevKinsta\CLI\Commands\Export;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @class SiteCommand
 */
class SiteCommand extends Command
{
    /**
     * Command name.
     *
     * @var string
     */
    protected static $defaultName = 'export:site';

    /**
     * Command description.
     *
     * @var string
     */
    protected static $defaultDescription = 'Export site files and database to given location';

    /**
     * Configure command arguments.
     */
    protected function configure(): void
    {
        $this->addArgument(
            'sitename',
            InputArgument::REQUIRED,
            'Site to export',
        );
        $this->addArgument(
            'path',
            InputArgument::OPTIONAL,
            'Export path.'
        );
    }

    /**
     * Execute container:restart command.
     *
     * @param  InputInterface  $input
     * @param  OutputInterface  $output
     *
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $siteName   = $input->getArgument('sitename');
        $exportPath = $input->getArgument('path');
        if (true === empty($exportPath)) {
            $exportPath = getcwd();
        }

        var_dump($exportPath);

        return Command::SUCCESS;
    }
}
