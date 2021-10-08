<?php

namespace DevKinsta\CLI\Traits;

/**
 * @trait DevKinstaPhpFpmTrait
 */
trait DevKinstaPHPTrait
{
    protected $supportedPhpVersions = array(
        '7.2',
        '7.3',
        '7.4',
        '8.0',
    );

    protected $supportedPhpModes = array(
        'cli',
        'fpm',
    );

    protected $phpIniConfigPath = '/etc/php/%version%/%mode%/';
    protected $phpIniExportPath = '/www/kinsta/private/devkinsta-cli/';

    public function exportConfigurations()
    {
        foreach ($this->supportedPhpVersions as $supportedPhpVersion) {
            foreach ($this->supportedPhpModes as $supportedPhpMode) {
                $containerPhpPath = strtr($this->phpIniConfigPath, array(
                    '%version%' => $supportedPhpVersion,
                    '%mode%'    => $supportedPhpMode,
                ));
            }
        }
    }
}
