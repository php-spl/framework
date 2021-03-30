<?php

namespace Web\Security;
use Exception;

class CSRF
{
    public $key = '_csrf';
    protected $token = '';

    public function input() {
        return "<input type='hidden' name={$this->key} value={$this->token}>\r\n";
    }
    
    /*
     * Token genereates a random key and puts in in a session
     */
    public function setToken($length = 32)
    {
        $this->token = base64_encode(openssl_random_pseudo_bytes($length));
        $_SESSION[$this->key] = $this->token;
        return $this->token;
    }

    /*
     * Get token
     */
    public function getToken()
    {
        if(isset($this->token)) {
            return $this->token;
        }
    }

    /*
     * Get token
     */
    public function getRequestToken()
    {
        if(isset($_POST[$this->key])) {
            return $_POST[$this->key];
        }

        return false;
    }

    /*
     * Get token
     */
    public function getSessionToken()
    {
        if(isset($_SESSION[$this->key])) {
            return $_SESSION[$this->key];
        }

        return false;
    }

    /*
     * Check if token exists
     */
    public function requestExists()
    {
        return ($this->getRequestToken()) ? true : false;
    }

    /*
     * Check if token exists
     */
    public function destroy()
    {
        if(isset($_SESSION[$this->key])) {
            unset($_SESSION[$this->key]);
        }

        if(isset($_POST[$this->key])) {
            unset($_POST[$this->key]);
        }

        return true;
    }

    /*
     * To see random keys generated
     */
    public function show($length = 32)
    {
        return base64_encode(openssl_random_pseudo_bytes($length));
    }
    
    /*
     * Checks if token session is set. Usefull for validating forms for CRSF
     */
    public function tokenVerify()
    {
        if($this->requestExists()) {

            if ($this->getRequestToken() != $this->getSessionToken()) {
                return false;
            } else {
                return true;
            }
        }

        return true;

    }

    public function check()
    {
        if($this->tokenVerify()) {
            unset($_SESSION[$this->key]);
            return true;
        } else {
            throw new Exception("CSRF error!");
        }
        
    }
    
}