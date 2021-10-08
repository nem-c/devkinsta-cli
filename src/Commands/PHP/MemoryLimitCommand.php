<?php

namespace DevKinsta\CLI\Commands\PHP;

use DevKinsta\CLI\Traits\DevKinstaPHPTrait;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @class MemoryLimitCommand
 */
class MemoryLimitCommand extends Command
{
    use DevKinstaPHPTrait;

    /**
     * Command name.
     *
     * @var string
     */
    protected static $defaultName = 'php:memory-limit';

    /**
     * Command description.
     *
     * @var string
     */
    protected static $defaultDescription = 'Set memory_limit value for all PHP versions';

    /**
     * Configure command arguments.
     */
    protected function configure(): void
    {
        $this->addArgument(
            'value',
            InputArgument::REQUIRED,
            'New value to use for memory_limit. Should be in [int][M|G] format.'
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
        return Command::SUCCESS;
    }
}
