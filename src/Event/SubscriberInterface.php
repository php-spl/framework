<?php

namespace Web\Event;

interface SubscriberInterface
{
    /**
     * @return array
     */
    public function getSubscribedEvents(EventManagerInterface $manager): array;
}
