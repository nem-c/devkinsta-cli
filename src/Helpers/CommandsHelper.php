<?php

namespace DevKinsta\CLI\Helpers;

use Symfony\Component\Finder\Finder;
use hanneskod\classtools\Iterator\ClassIterator;

class CommandsHelper
{
    /**
     * Return list of all commands FQDN paths.
     *
     * @return array
     */
    public static function getAvailableCommands(): array
    {
        $finder   = new Finder();
        $iterator = new ClassIterator($finder->in('src'.DIRECTORY_SEPARATOR.'Commands'));

        $commandsFqdn = array();

        foreach ($iterator->getClassMap() as $classname => $splFileInfo) {
            array_push($commandsFqdn, $classname);
        }

        return $commandsFqdn;
    }
}
