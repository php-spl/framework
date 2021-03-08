<?php

namespace Web\Security;

class CSRF
{
    protected $key = '';

    public function __construct($key = '')
    {
        $this->key = $key;

    }

    public function __toString() {
        return "<input type='hidden' name={$this->key} value={$this->getSessionToken()}>\r\n";
    }
    
    /*
     * Token genereates a random key and puts in in a session
     */
    public function setToken($length = 32)
    {
        if(!isset($_SESSION[$this->key])) {
            return $_SESSION[$this->key] = base64_encode(openssl_random_pseudo_bytes($length));
        }

        return $this->getSessionToken();
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
            return true;
        }

        if(isset($_POST[$this->key])) {
            return $_POST[$this->key];
            return true;
        }

        return false;
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
    public function verify()
    {
        if ($this->getRequestToken() === $this->getSessionToken()) {
            unset($_SESSION[$this->key]);
            return true;
        }

        return false;
    }
    

}