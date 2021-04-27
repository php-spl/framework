<?php

namespace Spl\Globals;

class Cookie
{
    public $expiry = (86400 * 1); // days
    public $path = '/';
    public $domain = false;
    public $secure = false;
    public $httpOnly = true;

    /*
     * Check if cookie exists by name
     */
    public function exists($name = null)
    {
        return (isset($_COOKIE[$name])) ? true : false;
    }

    /*
     * Check if cookie exists by name
     */
    public function has($name = null)
    {
        return $this->exists($name);
    }

    /*
     * Get cookie value by name
     */
    public static function get($name = null)
    {
        return $_COOKIE[$name];
    }

    public function all()
    {
        return $_COOKIE;
    }

    /*
     * Set a cookie and exipiry
     */
    public function set($name, $value, $expiry = false)
    {
        if($expiry) {
            self::$expiry = $expiry;
        }

        if (setcookie($name, $value, time() + $this->expiry, $this->path, $this->domain, $this->secure, $this->httpOnly)) {
            return true;
        }
        
        return false;
    }
    
    /*
     * Delete cookie by setting expiry to zero
     */
    public function delete($name)
    {
        if($this->has($name)) {
            $this->set($name, '', time() - 1);
            return false;
        }
        return false;
    }

}