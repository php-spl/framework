<?php

namespace Web\Error\Writers;

use Web\Error\Interfaces;
use Web\Error\Traits;

abstract class Writer implements Interfaces\Writer
{
    use Traits\Helper;

    /**
     * @var callable
     */
    protected $formatter;

    public function setFormatter(callable $formatter)
    {
        $this->formatter = $formatter;
    }

    abstract public function __invoke(array $record): bool;
}
