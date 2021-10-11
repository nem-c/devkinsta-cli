<?php

namespace DevKinsta\CLI\Commands\PHP;

use DevKinsta\CLI\Services\DevKinstaService;
use DevKinsta\CLI\Traits\DevKinstaPHPTrait;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WriteiniFile\ReadiniFile;
use WriteiniFile\WriteiniFile;

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

        $this->addOption(
            'mode',
            null,
            InputOption::VALUE_REQUIRED,
            'PHP Mode to update PHP setting for',
            'fpm'
        );

        $this->addOption(
            'skip-export',
            null,
            InputOption::VALUE_REQUIRED,
            'Export files before updating',
            false
        );

        $this->addOption(
            'skip-container-restart',
            null,
            InputOption::VALUE_REQUIRED,
            'Restart container after updating value.',
            false
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
        $phpVariableToUpdate  = trim($input->getArgument('variable'));
        $phpValueToUpdate     = trim($input->getArgument('value'));
        $phpMode              = trim($input->getOption('mode'));
        $skipExport           = filter_var($input->getOption('skip-export'), FILTER_VALIDATE_BOOLEAN);
        $skipContainerRestart = filter_var($input->getOption('skip-container-restart'), FILTER_VALIDATE_BOOLEAN);

        if (false === $this->isSupportedPHPMode($phpMode)) {
            throw new Exception('Unsupported PHP mode: '.$phpMode);
        }

        if (false === $skipExport) { // don't skip export.
            $this->exportPHPConfigurations('php.ini', $output);
        }

        $output->writeln(
            sprintf('Updating "%s" to "%s" for PHP mode %s', $phpVariableToUpdate, $phpValueToUpdate, $phpMode)
        );

        foreach ($this->getSupportedPHPVersions() as $supportedPhpVersion) {
            $localIniPath = DevKinstaService::getLocalDirPath();
            $localIniPath .= $this->getPHPConfigPath($supportedPhpVersion, $phpMode);
            $localIniPath .= 'php.ini';

            $localIniPath = str_replace('//', '/', $localIniPath);

            $this->updatePhpVariable($localIniPath, $phpVariableToUpdate, $phpValueToUpdate, $output);
        }

        $this->restorePHPConfigurations('php.ini', $output);

        if (false === $skipContainerRestart) { // don't skip container restart.
            $restartContainerCommand     = $this->getApplication()->find('container:restart');
            $restartContainerCommandArgs = new ArrayInput(array(
                'name' => 'devkinsta_nginx',
            ));

            $restartContainerCommand->run($restartContainerCommandArgs, $output);
        }

        return Command::SUCCESS;
    }

    /**
     * Update php variable in given INI path.
     *
     * @param  string  $iniPath
     * @param  string  $variable
     * @param  string  $value
     *
     * @throws Exception
     */
    private function updatePhpVariable(
        string $iniPath,
        string $variable,
        string $value,
        OutputInterface $output = null
    ): void {
        $iniFileSettings = ReadiniFile::get($iniPath);
        $variableGroup   = '';
        $variableName    = '';

        list($variableGroup, $variableName) = explode('.', $variable);

        if (true === empty($variableGroup) || true === empty($variableName)) {
            throw new Exception('Variable " '.$variable.'" is not in valid format. Use group.name (ex. PHP.memory_limit) instead.');
        }

        if (false === isset($iniFileSettings[$variableGroup][$variableName])) {
            $this->maybeUncommentVariable($iniPath, $variableName);
            $iniFileSettings = ReadiniFile::get($iniPath);
        }

        $iniFileSettings[$variableGroup][$variableName] = $value;

        if (false === is_null($output)) {
            $output->write('Updating local INI file '.$iniPath.' ... ');
        }

        $iniFile = new WriteIniFile($iniPath);
        $iniFile->create($iniFileSettings)->write();

        if (false === is_null($output)) {
            $output->writeln('DONE');
        }
    }

    private function maybeUncommentVariable(string $iniPath, string $variableName): void
    {

    }
}
