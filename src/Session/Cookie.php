<?php

namespace Web\Session;

class Cookie
{
    static $expiry = (86400 * 1); // days
    static $path = '/';
    static $domain = false;
    static $secure = false;
    static $httpOnly = true;

    /*
     * Check if cookie exists by name
     */
    public static function has($name)
    {
        return (isset($_COOKIE[$name])) ? true : false;
    }

    /*
     * Get cookie value by name
     */
    public static function get($name)
    {
        return $_COOKIE[$name];
    }

    /*
     * Set a cookie and exipiry
     */
    public static function set($name, $value, $expiry = false)
    {
        if($expiry) {
            self::$expiry = $expiry;
        }

        if (setcookie($name, $value, time() + self::$expiry, self::$path, self::$domain, self::$secure, self::$httpOnly)) {
            return true;
        }
        
        return false;
    }
    
    /*
     * Delete cookie by setting expiry to zero
     */
    public static function delete($name)
    {
        self::set($name, '', time() - 1);
    }

}