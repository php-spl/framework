<?php

namespace Web\Log\Interfaces;

interface Writer extends HelperAware
{
    public function setFormatter(callable $formatter);

    public function __invoke(array $record);
}
