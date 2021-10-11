<?php

namespace DevKinsta\CLI\Traits;

use DevKinsta\CLI\Services\DevKinstaService;

/**
 * @trait SitesTrait
 */
trait SitesTrait
{
    private function getCurrentSitesConfigPath(): string
    {
        $sitesPath = DevKinstaService::getConfigItem('projectPath');

        return $sitesPath.DIRECTORY_SEPARATOR.'kinsta'.DIRECTORY_SEPARATOR.'sites.ini';
    }
}
