<?php

namespace CliHelper;

use CliHelper\Command\NewCommand;
use CliHelper\Command\PackCommand;
use Exception;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class HelperApplication extends Application
{
    public const VERSION = '0.1.0';

    public function __construct()
    {
        parent::__construct('cli-helper', self::VERSION);
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $output = $output === null ? new ConsoleOutput() : $output;
        $this->eventCheck($output);
        $this->addCustomCommands();
        return parent::run($input, $output);
    }

    /**
     * @throws Exception
     */
    private function eventCheck(OutputInterface $output)
    {
        if (PHP_SAPI !== 'cli' && PHP_SAPI !== 'micro') {
            throw new Exception('This script can only be run from the command line.');
        }
        define('WORKING_DIR', getcwd());
        define('ROOT_DIR', dirname(__DIR__, 2));

        if (extension_loaded('pcntl')) {
            pcntl_signal(SIGINT, function () use ($output) {
                $output->writeln('<error>Interrupted by user, rolling back all changes.</error>');
                exit(255);
            });
        }
    }

    private function addCustomCommands()
    {
        $this->add(new NewCommand());
        $this->add(new PackCommand());
    }
}