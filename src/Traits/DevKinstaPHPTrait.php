<?php

namespace DevKinsta\CLI\Traits;

use DevKinsta\CLI\Services\DockerService;
use Exception;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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

    protected $phpConfigPathTemplate = '/etc/php/%version%/%mode%/';
    protected $exportPath = '/www/kinsta/private/.devkinsta-cli/';

    /**
     * Get list of supported PHP versions.
     *
     * @return string[]
     */
    public function getSupportedPHPVersions(): array
    {
        return $this->supportedPhpVersions;
    }

    /**
     * Get list of supported PHP modes.
     *
     * @return string[]
     */
    public function getSupportedPHPModes(): array
    {
        return $this->supportedPhpModes;
    }

    /**
     * Is given mode supported PHP mode in DevKinsta_FPM php configurations.
     *
     * @param  string  $mode
     *
     * @return bool
     */
    public function isSupportedPHPMode(string $mode): bool
    {
        return in_array($mode, $this->supportedPhpModes, true);
    }

    public function getPHPConfigPath(string $version, string $mode): string
    {
        return strtr($this->phpConfigPathTemplate, array(
            '%version%' => $version,
            '%mode%'    => $mode,
        ));
    }

    /**
     * Export php configuration from /etc/php to /www/kinsta/private/.devkinsta-cli
     * Makes configuration files available to parent machine for editing and altering.
     *
     * @return string[]
     * @throws Exception
     */
    public function exportPHPConfigurations(string $filename = 'php.ini', OutputInterface $output = null): array
    {
        if (false === is_null($output)) {
            $output->write('Exporting current php.ini files ... ');
        }
        $exportedFiles = array();

        foreach ($this->getSupportedPHPVersions() as $supportedPhpVersion) {
            foreach ($this->getSupportedPHPModes() as $supportedPhpMode) {
                $containerPhpPath = $this->getPHPConfigPath($supportedPhpVersion, $supportedPhpMode);

                // fix path - remove duplicated slash.
                $exportPath = str_replace('//', '/', $this->exportPath.$containerPhpPath);

                $this->makeExportDir($exportPath);
                $this->exportPHPConfiguration($containerPhpPath, $exportPath, $filename);

                $exportedFiles[] = $exportPath.$filename;
            }
        }

        if (false === is_null($output)) {
            $output->writeln('DONE');
        }

        return $exportedFiles;
    }

    /**
     * Restore php configuration to /etc/php from /www/kinsta/private/.devkinsta-cli
     * Restores configuration files available from parent machine to container.
     *
     * @return string[]
     *
     * @throws Exception
     */
    public function restorePHPConfigurations(string $filename = 'php.ini', OutputInterface $output = null): array
    {
        if (false === is_null($output)) {
            $output->write('Restoring php.ini files ... ');
        }

        $restoredFiles = array();

        foreach ($this->getSupportedPHPVersions() as $supportedPhpVersion) {
            foreach ($this->getSupportedPHPModes() as $supportedPhpMode) {
                $containerPhpPath = $this->getPHPConfigPath($supportedPhpVersion, $supportedPhpMode);

                // fix path - remove duplicated slash.
                $exportPath = str_replace('//', '/', $this->exportPath.$containerPhpPath);

                $this->restorePHPConfiguration($containerPhpPath, $exportPath, $filename);

                $restoredFiles[] = $containerPhpPath;
            }
        }

        if (false === is_null($output)) {
            $output->writeln('DONE');
        }

        return $restoredFiles;
    }

    /**
     * Create export directory.
     *
     * @param  string  $exportPath
     *
     * @throws Exception
     */
    private function makeExportDir(
        string $exportPath
    ): void {
        $mkdirProcess = $this->createDockerProcess(array(
            'mkdir',
            '-p',
            $exportPath,
        ));

        $mkdirProcess->run();
        if (false === $mkdirProcess->isSuccessful()) {
            throw new ProcessFailedException($mkdirProcess);
        }
    }

    /**
     * Copy PHP config file to export path from container path.
     *
     * @param  string  $containerPhpPath
     * @param  string  $exportPath
     * @param  string  $configName
     *
     * @throws Exception
     */
    private function exportPHPConfiguration(
        string $containerPhpPath,
        string $exportPath,
        string $configName
    ): void {
        $copyProcess = $this->createDockerProcess(array(
            'cp',
            $containerPhpPath.$configName,
            $exportPath.$configName,
            '-f',
        ));

        $copyProcess->run();

        if (false === $copyProcess->isSuccessful()) {
            throw new ProcessFailedException($copyProcess);
        }
    }

    /**
     * Restore PHP config file from export path to container path.
     *
     * @param  string  $containerPhpPath
     * @param  string  $exportPath
     * @param  string  $configName
     *
     * @throws Exception
     */
    private function restorePHPConfiguration(
        string $containerPhpPath,
        string $exportPath,
        string $configName
    ): void {
        $copyProcess = $this->createDockerProcess(array(
            'cp',
            $exportPath.$configName,
            $containerPhpPath.$configName,
            '-f',
        ));

        $copyProcess->run();

        if (false === $copyProcess->isSuccessful()) {
            throw new ProcessFailedException($copyProcess);
        }
    }

    /**
     * Create command wrapped for docker exec for devkinsta_fpm container.
     *
     * @param  array  $command
     *
     * @return Process
     * @throws Exception
     */
    private function createDockerProcess(array $command): Process
    {
        $docker = new DockerService();

        $command = array_merge(
            array(
                $docker->getDockerExecPath(),
                'exec',
                '-i',
                'devkinsta_fpm',
            ),
            $command
        );

        return new Process($command);
    }
}
