<?php

namespace DevKinsta\CLI\Services;

use Dflydev\DotAccessData\Data;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Tivie\OS\Detector;

/**
 * @class DevKinstaService
 */
class DevKinstaService
{
    /**
     * Singleton service instance.
     *
     * @var DevKinstaService
     */
    public static $instance = null;

    /**
     * Data object.
     *
     * @var Data
     */
    protected $data;

    /**
     * Get running instance and create if not initialized.
     *
     * @return DevKinstaService
     */
    public static function instance(): DevKinstaService
    {
        if (false === self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Instance constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->data = new Data();
        $this->loadConfig();
    }

    /**
     * @param  string  $configItem
     *
     * @return mixed
     */
    public static function getConfigItem(string $configItem)
    {
        $service = self::instance();

        return $service->data->get($configItem);
    }

    public static function getLocalDirPath(): string
    {
        $fileSystem = new Filesystem();

        $backupDir = self::getConfigItem('projectPath').DIRECTORY_SEPARATOR.'private'.DIRECTORY_SEPARATOR.'.devkinsta-cli'.DIRECTORY_SEPARATOR;
        $fileSystem->mkdir($backupDir);

        return $backupDir;
    }

    /**
     * @throws Exception
     */
    public function loadConfig(): void
    {
        $configPath = $this->getConfigPath();

        $configFileContent = file_get_contents($configPath);
        if (true === empty($configFileContent)) {
            throw new Exception('DevKinsta config is empty.');
        }

        $configFileData = json_decode($configFileContent, true);

        $this->data->import($configFileData);
    }

    /**
     * @throws Exception
     */
    public function getConfigPath(): string
    {
        $osDetector = new Detector();

        if ($osDetector->isOSX()) {
            $path = $this->getConfigPathOSX();
        } elseif ($osDetector->isWindowsLike()) {
            $path = $this->getConfigPathWindowsLike();
        } elseif ($osDetector->isUnixLike()) {
            $path = $this->getConfigPathUnixLike();
        } else {
            throw new Exception('Unsupported OS');
        }

        return $path;
    }

    private function getConfigPathOSX(): string
    {
        $user = get_current_user();

        return sprintf('/Users/%s/Library/Application Support/DevKinsta/config.json', $user);
    }

    /**
     * @throws Exception
     */
    private function getConfigPathWindowsLike(): string
    {
        throw new Exception('Windows is still not supported');
    }

    /**
     * @throws Exception
     */
    private function getConfigPathUnixLike(): string
    {
        throw new Exception('Unix-like systems are still not supported');
    }
}
