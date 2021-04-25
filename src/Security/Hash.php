<?php

namespace Spl\Security;

class Hash
{
    public static $algo = 'sha256';
    
    public static function crypt($string, $salt = '') 
    {
        return crypt($string . $salt, '$2y$10$' . $salt);
    }

    public static function random($length = 32) 
    {
        return strtr(substr(base64_encode(openssl_random_pseudo_bytes($length)),0,22), '+', '.');
    }

    public static function make($string, $key = false, $random = false) 
    {
        if($key) {
            return hash(self::$algo, $string . $key);
        }

        if($random) {
            return hash(self::$algo, $string . self::random());
        }
        
        return hash(self::$algo, $string);
    }

    public static function unique() 
    {
        return static::make(uniqid());
    }

    public static function equals($hash, $sig)
    {
       return hash_equals(static::make($hash), $sig);
    }

}