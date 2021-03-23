<?php

namespace Web\Security;
use Exception;

class CSRF
{
    protected $key = '';
    protected $token = '';

    public function __construct($key = '')
    {
        $this->key = $key;

    }

    public function __toString() {
        return "<input type='hidden' name={$this->key} value={$this->token}>\r\n";
    }
    
    /*
     * Token genereates a random key and puts in in a session
     */
    public function setToken($length = 32)
    {
        return $this->token = $_SESSION[$this->key] = base64_encode(openssl_random_pseudo_bytes($length));

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
    public function verify()
    {
        if ($this->getRequestToken() === $this->getSessionToken()) {
            unset($_SESSION[$this->key]);
            return true;
        }

        return false;
    }

    public function check()
    {
        if($this->requestExists()) {
            if(!$this->verify()) {
                throw new Exception("CSRF error!");
            }
            } else {
                $this->setToken();
            }
        }
    }
    
}