<?php

namespace DevKinsta\CLI\Commands\Kinsta;

use DevKinsta\CLI\Services\DevKinstaService;
use DevKinsta\CLI\Traits\SitesTrait;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use WriteiniFile\ReadiniFile;
use WriteiniFile\WriteiniFile;

/**
 * @class RebuildSitesIniCommand
 */
class RebuildCommand extends Command
{
    use SitesTrait;

    /**
     * Command name.
     *
     * @var string
     */
    protected static $defaultName = 'sites:rebuild';

    /**
     * Command description.
     *
     * @var string
     */
    protected static $defaultDescription = 'Rebuild sites.ini based on sites in config.json';

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
        // backup current config first.
        $this->backupCurrent($output);

        // read current sites.ini
        $sitesConfig = ReadiniFile::get($this->getCurrentSitesConfigPath());

        // this is not changeable from here, and it will be kept as constant here.
        $sitesConfigDefault = $sitesConfig['DEFAULT'];

        $newSitesConfig = array();

        // load all sites from devkinsta config.json.
        $sites = DevKinstaService::getConfigItem('sites');
        foreach ($sites as $site) {
            $newSitesConfig[$site['name']] = array(
                'domain'              => $site['url'],
                'php_version'         => $site['web']['version'],
                'is_multisite_subdir' => '1', // Should be confirmed is different value is available.
            );
        }

        // sort by name.
        ksort($newSitesConfig);

        // prepend DEFAULT category.
        $newSitesConfig = array_merge(array('DEFAULT' => $sitesConfigDefault), $newSitesConfig);

        $sitesConfig = new WriteIniFile($this->getCurrentSitesConfigPath());
        $sitesConfig->create($newSitesConfig)->write();

        $output->writeln('Successfully updated DevKinsta\'s sites.ini ');

        $restartContainerCommand     = $this->getApplication()->find('container:restart');
        $restartContainerCommandArgs = new ArrayInput(array(
            'name' => 'devkinsta_nginx',
        ));

        $restartContainerCommand->run($restartContainerCommandArgs, $output);

        return Command::SUCCESS;
    }

    protected function backupCurrent(OutputInterface $output)
    {
        $fileSystem = new Filesystem();
        $backupTime = time();

        $backupDir        = DevKinstaService::getBackupDirPath();
        $backupConfigPath = $backupDir.'sites.ini.'.$backupTime;

        $fileSystem->mkdir($backupDir);

        $fileSystem->copy($this->getCurrentSitesConfigPath(), $backupConfigPath);
        $output->writeln('Backup of sites.ini file available at '.$backupConfigPath);
    }
}
