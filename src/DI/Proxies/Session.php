<?php

namespace Spl\DI\Proxies;

use Spl\DI\Proxy;

class Session extends Proxy
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getProxyAccessor()
    {
        return 'session';
    }
}