<?php

namespace DevKinsta\CLI\Commands\Container;

use DevKinsta\CLI\Services\DockerService;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * @class RestartCommand
 */
class RestartCommand extends Command
{
    /**
     * Command name.
     *
     * @var string
     */
    protected static $defaultName = 'container:restart';

    /**
     * Command description.
     *
     * @var string
     */
    protected static $defaultDescription = 'Restart docker container';

    /**
     * DockerService instance.
     *
     * @var DockerService
     */
    protected $docker;

    /**
     * Allowed container names array.
     *
     * @var string[]
     */
    protected $allowed_containers = array(
        'devkinsta_fpm',
        'devkinsta_nginx',
        'devkinsta_mailhog',
        'devkinsta_adminer',
        'devkinsta_db',
    );

    /**
     * Constructor.
     *
     * @param  string|null  $name
     */
    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->docker = new DockerService();
    }

    /**
     * Configure command arguments.
     */
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Container name to restart');
    }

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
        $container_name = strtolower(trim($input->getArgument('name')));
        if (false === $this->is_container_name_allowed($container_name)) {
            $output->writeln(sprintf('Container name "%s" is not supported', $container_name));

            return Command::FAILURE;
        }

        $process = new Process(array($this->docker->getDockerExecPath(), 'restart', $container_name));
        $process->run();

        if (false === $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        if ($container_name === strtolower(trim($process->getOutput()))) {
            $output->writeln(sprintf('Container "%s" has been restarted', $container_name));
        }

        return Command::SUCCESS;
    }

    /**
     * Check is passed string allowed among allowed container names.
     *
     * @param  string  $container_name
     *
     * @return bool
     */
    private function is_container_name_allowed(string $container_name): bool
    {
        return (true === in_array($container_name, $this->allowed_containers, true));
    }

}
