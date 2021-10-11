<?php

namespace DevKinsta\CLI\Commands\PHP;

use DevKinsta\CLI\Traits\DevKinstaPHPTrait;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @class MaxFileSizeCommand
 */
class MaxFileUploadSizeCommand extends Command
{
    use DevKinstaPHPTrait;

    /**
     * Command name.
     *
     * @var string
     */
    protected static $defaultName = 'php:max-file-upload-size';

    /**
     * Command description.
     *
     * @var string
     */
    protected static $defaultDescription = 'Set post_max_size and upload_max_filesize values for all PHP versions';

    /**
     * Configure command arguments.
     */
    protected function configure(): void
    {
        $this->addArgument(
            'value',
            InputArgument::REQUIRED,
            'New value to use for post_max_size and upload_max_filesize. Should be in [int][M|G] format.'
        );

        $this->addOption(
            'mode',
            null,
            InputOption::VALUE_REQUIRED,
            'PHP Mode to update PHP setting for',
            'fpm'
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
        $phpSetCommand     = $this->getApplication()->find('php:set');
        $phpSetCommandArgs = new ArrayInput(array(
            'variable'                 => 'PHP.post_max_size',
            'value'                    => trim($input->getArgument('value')),
            '--mode'                   => strtolower(trim($input->getOption('mode'))),
            '--skip-container-restart' => true,
        ));

        $phpSetCommand->run($phpSetCommandArgs, $output);

        $phpSetCommand     = $this->getApplication()->find('php:set');
        $phpSetCommandArgs = new ArrayInput(array(
            'variable'      => 'PHP.upload_max_filesize',
            'value'         => trim($input->getArgument('value')),
            '--mode'        => strtolower(trim($input->getOption('mode'))),
            '--skip-export' => true,
        ));

        $phpSetCommand->run($phpSetCommandArgs, $output);

        return Command::SUCCESS;
    }
}
