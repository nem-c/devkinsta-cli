<?php

namespace DevKinsta\CLI\Commands\Kinsta;

use DevKinsta\CLI\Services\DevKinstaService;
use DevKinsta\CLI\Traits\SitesTrait;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use WriteiniFile\ReadiniFile;
use WriteiniFile\WriteiniFile;

/**
 * @class RebuildSitesIniCommand
 */
class ListCommand extends Command
{
    use SitesTrait;

    /**
     * Command name.
     *
     * @var string
     */
    protected static $defaultName = 'sites:list';

    /**
     * Command description.
     *
     * @var string
     */
    protected static $defaultDescription = 'List all sites available in sites.ini';

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
        // read current sites.ini
        $sitesConfig = ReadiniFile::get($this->getCurrentSitesConfigPath());

        unset($sitesConfig['DEFAULT']);

        $sitesData = array();
        foreach ($sitesConfig as $siteName => $siteConfig) {
            $sitesData[] = array(
                $siteName,
                $siteConfig['domain'],
                $siteConfig['php_version'],
            );
        }

        $sitesTable = new Table($output);
        $sitesTable->setStyle('box');
        $sitesTable->setHeaders(array('Site Name', 'Domain', 'PHP Version'));
        $sitesTable->setRows($sitesData);

        $sitesTable->render();

        return Command::SUCCESS;
    }
}