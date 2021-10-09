<?php

namespace DevKinsta\CLI\Commands\PHP;

use DevKinsta\CLI\Services\DevKinstaService;
use DevKinsta\CLI\Traits\DevKinstaPHPTrait;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @class SetCommand
 */
class SetCommand extends Command
{
    use DevKinstaPHPTrait;

    /**
     * Command name.
     *
     * @var string
     */
    protected static $defaultName = 'php:set';

    /**
     * Command description.
     *
     * @var string
     */
    protected static $defaultDescription = 'Set php ini to given value for all PHP versions';

    /**
     * Configure command arguments.
     */
    protected function configure(): void
    {
        $this->addArgument(
            'variable',
            InputArgument::REQUIRED,
            'Setting value for variable.'
        );

        $this->addArgument(
            'value',
            InputArgument::REQUIRED,
            'New value to use for variable.'
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
        $phpVariableToUpdate = strtolower(trim($input->getArgument('variable')));
        $phpValueToUpdate    = strtolower(trim($input->getArgument('value')));

        $output->write('Exporting current php.ini files...');
        $this->exportPHPConfigurations();
        $output->writeln(' DONE!');

        foreach ($this->getSupportedPHPVersions() as $supportedPhpVersion) {
            foreach ($this->getSupportedPHPModes() as $supportedPhpMode) {
                DevKinstaService::getLocalDirPath();
            }
        }

        return Command::SUCCESS;
    }
}
