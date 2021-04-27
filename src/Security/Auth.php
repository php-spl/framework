<?php

namespace Spl\Security;

use Spl\Database\Model;
use Spl\Globals\Session;
use Spl\Globals\Cookie;

class Auth 
{

    public $name = 'user'; 

    public $user_id = 'id';
    public $email = 'email';
    public $username = 'username';

    // Cookies
    public $expiry = (86400 * 14); // days

    protected $user;
    protected $session;
    protected $cookie;

    public function __construct(Model $user, Session $session, Cookie $cookie)
    {
        $this->user = $user;
        $this->session = $session;
        $this->cookie = $cookie;
    }

    public function user() 
    {
        if($this->check()) { 
            return $this->user->where($this->user_id, $_SESSION[$this->name])->first();
        }
    }

    public function check() 
    {
        if($this->session->has($this->name) || $this->cookie->has($this->name)) {
            return true;
        }

        return false;
    }

    public function hash($password, $algo = PASSWORD_DEFAULT)
    {
       return password_hash($password, $algo);
    }

    public function attempt($emailOrUsername, $password, $remember = false) 
    {
        if($this->check()) {
            return true;
        }

        $user = $this->user
        ->select()
        ->where($this->email, $emailOrUsername)
        ->orWhere($this->username, $emailOrUsername)
        ->first();

        if (!$user) {
            return false;
        }

        if (password_verify($password, $user->password)) {
            $user_id = $user->{$this->user_id};

            $_SESSION[$this->name] = $user_id;
            
            if($remember) {
                $this->cookie->set($this->name, $user_id, $this->expiry);
            }

            return true;
        }

        return false;
    }

    public function validate($emailOrUsername, $password) 
    {
        if($emailOrUsername) {
            $user = $this->user
            ->select()
            ->where($this->email, $emailOrUsername)
            ->orWhere($this->username, $emailOrUsername)
            ->first();

            if (!$user) {
                return false;
            }
        }

        if($password) {
            if (password_verify($password, $user->password)) {
                return true;
            }
        }

        return false;
    }

    public function logout() 
    {
        if($this->check()) {

            if($this->session->has($this->name)) {
                $this->session->delete($this->name);    
            }

            if($this->cookie->has($this->name)) {
                $this->cookie->delete($this->name);
            }

            return true;
        }
        
        return false;
    }

}