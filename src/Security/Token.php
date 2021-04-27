<?php

namespace Spl\Security;

use Spl\Globals\Session;

class Token extends Session
{
    public $name = '_token';

    public function csrf()
    {
        $name = $this->name;
        $token = $this->create();

        return "<input type='hidden' name='{$name}' value='{$token}'>" . PHP_EOL;
    }

    public  function create() {
        return $this->put($this->name, base64_encode(openssl_random_pseudo_bytes(32)));
    }

    public function validate($token){
        $name = $this->name;
        if ($this->has($name) && $token === $this->get($name))  {
            $this->delete($name);
            return true;
        }
        return false;
    }
}

