<?php

namespace Spl\Error\Interfaces;

interface Writer extends HelperAware
{
    public function setFormatter(callable $formatter);

    public function __invoke(array $record);
}
