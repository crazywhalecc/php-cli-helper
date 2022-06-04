<?php

namespace CliHelper\Tools;

use Symfony\Component\Console\Helper\ProgressBar;

class SeekableArrayIterator implements \Iterator
{
    private $array;
    private $offset = 0;
    private $length;
    /** @var ProgressBar|null */
    private $bar;

    public function __construct(array $array, ?ProgressBar $bar = null)
    {
        $this->array = $array;
        $this->bar = $bar;
        $this->length = count($array);
        $this->bar->setMaxSteps($this->length);
        $this->bar->start();
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        if ($this->bar !== null) {
            $this->bar->setProgress($this->offset + 1);
        }
        return current($this->array);
    }

    #[\ReturnTypeWillChange]
    public function next()
    {
        next($this->array);
        $this->offset++;
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->array);
    }

    #[\ReturnTypeWillChange]
    public function valid()
    {
        return isset($this->array[key($this->array)]);
    }

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        reset($this->array);
        $this->offset = 0;
    }
}