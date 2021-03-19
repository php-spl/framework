<?php

namespace Web\Auth;

use User;

class Auth {

    public $session = 'user'; 

    protected $user;

    public function __construct()
    {
        
    }

    public function user() {
        return User::find($_SESSION[$this->session]);
    }

    public function check() {
        return isset($_SESSION[$this->session]);
    }

    public function attempt($email, $password) {

        $user = User::where('email', $email)->first();

        if (!$user) {
            return false;
        }

        if (password_verify($password, $user->password)) {
            $_SESSION[$this->session] = $user->id;
            return true;
        }

        return false;
    }

    public function logout() {
        unset($_SESSION[$this->session]);
    }

}