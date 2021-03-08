<?php

namespace Web\Log\Writers;

use Web\Log\Interfaces;
use Web\Log\Traits;

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
