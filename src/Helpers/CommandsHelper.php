<?php

namespace DevKinsta\CLI\Helpers;

use Phar;
use Symfony\Component\Finder\Finder;
use hanneskod\classtools\Iterator\ClassIterator;

class CommandsHelper
{
    /**
     * Check is phar used to run script.
     *
     * @return bool
     */
    public static function isPhar(): bool
    {
        $isPhar = false;

        if (false === empty(Phar::running())) {
            $isPhar = true;
        }

        return $isPhar;
    }

    /**
     * Return list of all commands FQDN paths.
     *
     * @return array
     */
    public static function getAvailableCommands(): array
    {
        $commandsPath = DEV_KINSTA_CLI_ROOT_DIR.'src'.DIRECTORY_SEPARATOR.'Commands';
        $finder       = new Finder();
        $iterator     = new ClassIterator(
            $finder->in(
                array(
                    $commandsPath,
                )
            )
        );

        $commandsFqdn = array();

        foreach ($iterator->getClassMap() as $classname => $splFileInfo) {
            array_push($commandsFqdn, $classname);
        }

        return $commandsFqdn;
    }
}
