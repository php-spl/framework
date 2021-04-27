<?php

namespace Spl\Globals;

class Post
{
    public function exists($key = null)
    {
        return !empty($_GET[$key]) ? true : false;
    }

    public function has($key = null)
    {
        return $this->exists($key);
    }

    public function value($key = null)
    {
        if ($this->has($key)) {
            return trim(filter_var($_POST[$key], FILTER_SANITIZE_STRING));
        } else {
            return false;
        }
    }

    public function all()
    {
        return $_POST;
    }
}