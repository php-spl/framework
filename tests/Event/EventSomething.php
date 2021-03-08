<?php

namespace Tests\Event;

use Web\Event\Event;

class EventSomething extends Event
{

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
