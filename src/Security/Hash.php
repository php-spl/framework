<?php

namespace Spl\Security;

class Hash
{
    public $algo = 'sha256';
    
    public function crypt($string, $salt = '') 
    {
        return crypt($string . $salt, '$2y$10$' . $salt);
    }

    public function random($length = 32) 
    {
        return strtr(substr(base64_encode(openssl_random_pseudo_bytes($length)),0,22), '+', '.');
    }

    public function make($string, $key = false, $random = false) 
    {
        if($key) {
            return hash($this->algo, $string . $key);
        }

        if($random) {
            return hash($this->algo, $string . $this->random());
        }
        
        return hash($this->algo, $string);
    }

    public function unique() 
    {
        return $this->make(uniqid());
    }

    public function equals($hash, $sig)
    {
       return hash_equals($this->make($hash), $sig);
    }

}