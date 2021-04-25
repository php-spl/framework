<?php

namespace Spl\Security;

use Spl\Globals\Session;

class CSRF
{
    public static $key = '_csrf';

    public static function token() {
        return Session::set(self::$key, base64_encode(openssl_random_pseudo_bytes(32)));
    }

    public static function check($token){
        if (Session::has(self::$key) && $token === Session::get(self::$key))  {
            Session::delete(self::$key);
            return true;
        }
        return false;
    }
}

