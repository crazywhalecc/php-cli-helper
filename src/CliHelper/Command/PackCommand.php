<?php

namespace CliHelper\Command;

use CliHelper\Tools\ArgFixer;
use CliHelper\Tools\DataProvider;
use CliHelper\Tools\SeekableArrayIterator;
use PhpCsFixer\PharChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class PackCommand extends Command
{
    protected static $defaultName = 'pack';

    protected static $defaultDescription = 'Pack directories and files into a phar archive.';

    protected function configure()
    {
        $this->addArgument('path', InputArgument::OPTIONAL, 'The file or directory to pack.');
        $this->addArgument('target', InputArgument::OPTIONAL, 'The file or directory to pack.');
        $this->addOption('auto-phar-fix', null, InputOption::VALUE_NONE, 'Automatically fix ini option.');
        $this->addOption('filter-regex', 'F', InputOption::VALUE_REQUIRED, 'Filter files by regex.');
        $this->addOption('entry', 'E', InputOption::VALUE_REQUIRED, 'The entry point of the phar.');
        $this->addOption('overwrite', 'W', InputOption::VALUE_NONE, 'Overwrite existing files.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // 第一阶段流程：如果没有写path，将会提示输入要打包的path
        $prompt = new ArgFixer($input, $output);
        // 首先得确认是不是关闭了readonly模式
        if (ini_get('phar.readonly') == 1) {
            if ($input->getOption('auto-phar-fix')) $ask = true;
            else $ask = $prompt->requireBool('<comment>pack command needs "phar.readonly" = "Off" !</comment>' . PHP_EOL . 'If you want to automatically set it and continue, just Enter', true);
            $output->writeln('<info>Now running command in child process.</info>');
            if ($ask) {
                global $argv;
                passthru(PHP_BINARY . ' -d phar.readonly=0 ' . implode(' ', $argv), $retcode);
                exit($retcode);
            }
        }
        // 获取路径
        $path = $prompt->requireArgument('path', 'Please input the path to pack', WORKING_DIR);
        // 如果是目录，则将目录下的所有文件打包
        $phar_path = $prompt->requireArgument('target', 'Please input the phar target filename', 'app.phar');

        $stub = $prompt->requireOption('entry', 'Please input the entry point of the phar (relative to the phar)', null, function($x) use ($path) {
            if (!is_string($x)) {
                throw new \RuntimeException('The entry file must be a string.');
            }
            if (!is_file($path . '/' . $x)) {
                if (is_file($path . '/' . $x . '.php')) {
                    $x .= '.php';
                } else {
                    throw new \RuntimeException('The entry file does not exist.');
                }
            }
            return $x;
        });

        if (DataProvider::isRelativePath($phar_path)) {
            $phar_path =  '/tmp/' . $phar_path;
        }
        if (file_exists($phar_path)) {
            $ask = $input->getOption('overwrite') ? true : $prompt->requireBool('<comment>The file "' . $phar_path . '" already exists, do you want to overwrite it?</comment>' . PHP_EOL . 'If you want to, just Enter', true);
            if (!$ask) {
                $output->writeln('<comment>User canceled.</comment>');
                return 1;
            }
            @unlink($phar_path);
        }
        $phar = new \Phar($phar_path);
        $phar->startBuffering();

        $all = DataProvider::scanDirFiles($path, true, true);
        sort($all);
        $map = [];
        foreach ($all as $v) {
            $map[$v] = $path . '/' . $v;
        }

        $output->writeln('<info>Start packing files...</info>');
        try {
            $phar->buildFromIterator(new SeekableArrayIterator($map, new ProgressBar($output)));
            $phar->setStub(
                "#!/usr/bin/env php\n" .
                $phar->createDefaultStub($stub)
            );
        } catch (\Throwable $e) {
            $output->writeln($e);
            return 1;
        }
        $phar->stopBuffering();
        $output->writeln(PHP_EOL . 'Done! Phar file is generated at "' . $phar_path . '".');
        return 0;
    }
}