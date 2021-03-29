<?php

namespace Web\Security;

class Hash
{
	public static function crypt($string, $salt = '') 
    {
        return crypt($string . $salt, '$2y$10$' . $salt);
    }

    public static function random($length = 32) 
    {
        return strtr(substr(base64_encode(openssl_random_pseudo_bytes($length)),0,22), '+', '.');
    }

    public static function make($string, $key = null) 
    {
        if($key) {
            return hash('sha256', $string . $key);
        } else {
            return hash('sha256', $string . self::random());
        }
    }

    public static function unique() 
    {
        return self::make(uniqid());
    }

    public function equals($hash, $sig)
    {
       return hash_equals(hash('sha256', $hash), $sig);
    }

}