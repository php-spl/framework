<?php

namespace Web\Security;

class Auth {

    public $session = 'user'; 
    public $user_id = 'id';
    public $email = 'email';
    public $username = 'username';

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function user() 
    {
        return $this->user->where($this->user_id, $_SESSION[$this->session])->first();
    }

    public function check() 
    {
        return isset($_SESSION[$this->session]);
    }

    public function hash($password, $algo = PASSWORD_DEFAULT)
    {
       return password_hash($password, $algo);
    }

    public function attempt($auth, $password) 
    {

        $user = $this->user
        ->where($this->email, $auth)
        ->orWhere($this->username, $auth)
        ->first();

        if (!$user) {
            return false;
        }

        if (password_verify($password, $user->password)) {
            $_SESSION[$this->session] = $user->{$this->user_id};
            return true;
        }

        return false;
    }

    public function logout() 
    {
        unset($_SESSION[$this->session]);
    }

}