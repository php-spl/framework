<?php

namespace Spl\Globals;

use SPL\Globals\Server;

class Http
{
    /**
     * Returns the http connection (such as keep-alive)
     */
    public static function connection()
    {
        return Server::connection();
    }

    /**
     *  Returns the Accept header from the current request
     */
    public static function accept()
    {
        return Server::accept();
    }

    /**
     *  Returns the Accept_Charset header from the current request (such as utf-8,ISO-8859-1)
     */
    public static function charset()
    {
        return Server::charset();
    }

    /**
     *  Returns the Host header from the current request
     */
    public static function host()
    {
        return Server::host();
    }

    /**
     *  Returns the complete URL of the current page (not reliable because not all user-agents support it)
     */
    public static function referer()
    {
        return Server::referer();
    }

    /**
     *  Returns the browser user agent
     */
    public static function agent()
    {
        return Server::agent();
    }

    public static function error()
    {
        return http_response_code();
    }
    
    public static function set($code)
    {
        return http_response_code($code);
    }

    /**
     *  Redirect user to location
     */
    public static function redirect($location, $include = '')
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