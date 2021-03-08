<?php

namespace Web\Security;

class Hash
{
	public static function make($string, $salt = '') {
        return crypt($string . $salt, '$2y$10$' . $salt);
    }

    public static function salt($length) {
        return strtr(substr(base64_encode(openssl_random_pseudo_bytes($length)),0,22), '+', '.');
    }

    public static function makeCookieHash($string, $salt = '') {
        return hash('sha256', $string . $salt);
    }

    public static function unique() {
        return self::makeCookieHash(uniqid());
    }

}