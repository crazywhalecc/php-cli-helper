<?php

namespace CliHelper\Tools;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class ArgFixer
{
    /**
     * @var InputInterface
     */
    private $input;
    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    public function requireArgument(string $string, string $prompt, $default = null)
    {
        if (($get = $this->input->getArgument($string)) === null) {
            if ($default !== null) {
                $prompt .= ' (default: <question>' . $default . '</question>)';
            }
            $prompt = '<comment>' . $prompt . '</comment>';
            $prompt .= PHP_EOL . '> ';
            $question = new Question($prompt, $default);
            $get = (new QuestionHelper())->ask($this->input, $this->output, $question);
        }
        return $get;
    }

    public function requireBool(string $prompt, bool $default = true)
    {
        $default_str = '[' . ($default ? 'Y' : 'y') . '/' . ($default ? 'n' : 'N') . ']';
        $question = new ConfirmationQuestion($prompt . ' ' . $default_str . ': ', $default);
        return (new QuestionHelper())->ask($this->input, $this->output, $question);
    }

    public function requireOption(string $string, string $prompt, $default = null, callable $validator = null)
    {
        if (($get = $this->input->getOption($string)) === null) {
            if ($default !== null) {
                $prompt .= ' (default: <question>' . $default . '</question>)';
            }
            $prompt = '<comment>' . $prompt . '</comment>';
            $prompt .= PHP_EOL . '> ';
            $question = new Question($prompt, $default);
            if ($validator !== null) {
                $question->setValidator($validator);
            }
            $get = (new QuestionHelper())->ask($this->input, $this->output, $question);
        }
        return $get;
    }
}