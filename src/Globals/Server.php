<?php

namespace Spl\Globals;

class Server
{
    /**
     * Returns the http connection (such as keep-alive)
     */
    public function connection()
    {
        return $_SERVER['HTTP_CONNECTION'];
    }

    /**
     * Returns the version of the Common Gateway Interface (CGI) the server is using
     */
    public function gateway()
    {
        return $_SERVER['GATEWAY_INTERFACE'];
    }

    /**
     * Returns the IP address of the host server
     */
    public function ip()
    {
        return $_SERVER['SERVER_ADDR'];
    }

    /**
     * Returns the name of the host server
     */
    public function name()
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * Returns the server identification string (such as Apache/2.2.24)
     */
    public function software()
    {
        return $_SERVER['SERVER_SOFTWARE'];
    }

    /**
     * Returns the request scheme
     */
    public function scheme()
    {
        return $_SERVER['REQUEST_SCHEME'];
    }

    /**
     * Returns the name and revision of the information protocol (such as HTTP/1.1)
     */
    public function protocal()
    {
        return $_SERVER['SERVER_PROTOCOL'];
    }

    /**
     * Returns the request method used to access the page (such as POST)
     */
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Returns the timestamp of the start of the request (such as 1377687496)
     */
    public function time()
    {
        return $_SERVER['REQUEST_TIME'];
    }

    /**
     * 	Returns the query string if the page is accessed via a query string
     */
    public function query()
    {
        return $_SERVER['QUERY_STRING'];
    }

    /**
     *  Returns the browser user agent
     */
    public function agent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }
    
    
    /**
     *  Returns the Accept header from the current request
     */
    public function accept()
    {
        return $_SERVER['HTTP_ACCEPT'];
    }

    /**
     *  Returns the Accept_Charset header from the current request (such as utf-8,ISO-8859-1)
     */
    public function charset()
    {
        return $_SERVER['HTTP_ACCEPT_CHARSET'];
    }

    /**
     *  Returns the Host header from the current request
     */
    public function host()
    {
        return $_SERVER['HTTP_HOST'];
    }

    /**
     *  Returns the complete URL of the current page (not reliable because not all user-agents support it)
     */
    public function referer()
    {
        return $_SERVER['HTTP_REFERER'];
    }

    /**
     *  Return current url
     */
    public function current()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
    
    /**
     *  Return root url
     */
    public function url($path = null)
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . $path;
    }    

    /**
     * ip: Returns the IP address from where the user is viewing the current page
     * host: Returns the Host name from where the user is viewing the current page
     */
    public function remote($server = 'ip')
    {
        switch ($server) {
            case 'ip':
                return $_SERVER['REMOTE_ADDR'];
                break;
            case 'host':
                return $_SERVER['REMOTE_HOST'];
                break;
            case 'port':
                return $_SERVER['REMOTE_PORT'];
                break;
            default:
                return false;
                break;
        }
    }

    /**
    *  Returns the value given to the SERVER_ADMIN directive in the web server configuration file (if your script runs on a virtual host, it will be the value defined for that virtual host)
    */
    public function admin()
    {
        return $_SERVER['SERVER_ADMIN'];
    }

    /**
    *  Returns the port on the server machine being used by the web server for communication (such as 80)
    */
    public function port()
    {
        return $_SERVER['SERVER_PORT'];
    }

    /**
    *  Returns the server version and virtual host name which are added to server-generated pages
    */
    public function sig()
    {
        return $_SERVER['SERVER_SIGNATURE'];
    }

    /**
    *  script: Returns the path of the current script
    *  system: Returns the file system based path to the current script
    */
    public function path($server = 'root')
    {
        switch ($server) {
            case 'file':
                return $_SERVER['SCRIPT_FILENAME'];
                break;
            case 'system':
                return $_SERVER['PATH_TRANSLATED'];
                break;
            case 'root':
                return $_SERVER['DOCUMENT_ROOT'];
                break;
            default:
                return false;
                break;
        }
    }

    /**
    *  Returns the URI of the current page
    */
    public function uri($server = 'request')
    {
        switch ($server) {
            case 'request':
                return $_SERVER['REQUEST_URI'];
                break;
            case 'script':
                return $_SERVER['SCRIPT_NAME'];
                break;
            case 'self':
                return $_SERVER['PHP_SELF'];
                break;
            default:
                return false;
                break;
        }
    }

}