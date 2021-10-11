<?php

namespace DevKinsta\CLI;

use Phar;
use Symfony\Component\Console\Application;
use DevKinsta\CLI\Helpers\CommandsHelper;

define('DEV_KINSTA_CLI_START', microtime(true));
define('DEV_KINSTA_CLI_ROOT_DIR', __DIR__.DIRECTORY_SEPARATOR);

require __DIR__.'/vendor/autoload.php';

$devKinstaCLI = new Application();

$commands = array(
    'DevKinsta\\CLI\\Commands\\Container\\RestartCommand',

    'DevKinsta\\CLI\\Commands\\Export\\SiteCommand',

    'DevKinsta\\CLI\\Commands\\PHP\\MaxFileUploadSizeCommand',
    'DevKinsta\\CLI\\Commands\\PHP\\MemoryLimitCommand',
    'DevKinsta\\CLI\\Commands\\PHP\\SetCommand',

    'DevKinsta\\CLI\\Commands\\Sites\\ListCommand',
    'DevKinsta\\CLI\\Commands\\Sites\\RebuildCommand',
);

// Finder and Iterator have problems when working in phar.
if (false === CommandsHelper::isPhar()) {
    $commands = CommandsHelper::getAvailableCommands();
}

foreach ($commands as $command) {
    $devKinstaCLI->add(new $command());
}

try {
    $devKinstaCLI->run();
} catch (\Exception $e) {
    die($e->getMessage() . PHP_EOL);
}
