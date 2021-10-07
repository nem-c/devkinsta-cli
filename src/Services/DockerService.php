<?php

namespace DevKinsta\CLI\Services;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Tivie\OS\Detector;
use Exception;

class DockerService
{
    /**
     * Get Docker exec path
     *
     * @return string
     * @throws Exception
     */
    public function getDockerExecPath(): string
    {
        $osDetector = new Detector();

        if ($osDetector->isOSX()) {
            $path = $this->getDockerExecPathOSX();
        } elseif ($osDetector->isWindowsLike()) {
            $path = $this->getDockerExecPathWindowsLike();
        } elseif ($osDetector->isUnixLike()) {
            $path = $this->getDockerExecPathUnixLike();
        } else {
            throw new Exception('Unsupported OS');
        }

        return $path;
    }

    /**
     * Get docker exec path for OSX.
     *
     * @return string
     */
    private function getDockerExecPathOSX(): string
    {
        $which_process = new Process(array('which', 'docker'));
        $which_process->run();
        if (false === $which_process->isSuccessful()) {
            throw new ProcessFailedException($which_process);
        }

        return trim($which_process->getOutput());
    }

    /**
     * Get docker exec path for Windows-ike systems.
     *
     * @return string
     * @throws Exception
     */
    private function getDockerExecPathWindowsLike(): string
    {
        throw new Exception('Windows is still not supported');
    }

    /**
     * Get docker exec path for Unix-like systems.
     *
     * @return string
     * @throws Exception
     */
    private function getDockerExecPathUnixLike(): string
    {
        throw new Exception('Unix-like systems are still not supported');
    }
}
