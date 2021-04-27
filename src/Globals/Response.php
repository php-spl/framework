<?php

namespace Spl\Globals;

use SPL\Globals\Server;

class Response
{
    protected $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function server()
    {
        return $this->server;
    }

    public function error()
    {
        return http_response_code();
    }
    
    public function set($code)
    {
        return http_response_code($code);
    }

    /**
     *  Redirect user to location
     */
    public function redirect($location, $include = '')
    {
        if ($location) {
            if (is_numeric($location)) {
                switch ($location) {
                    case 404:
                        header('HTTP/1.0 404 Not Found');
                        include $include;
                        exit();
                        break;
                }
            }
            header('Location: ' . $location);
            exit();
        }
    }
}