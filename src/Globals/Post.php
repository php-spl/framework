<?php

namespace Spl\Globals;

class Post
{
    public static function has($key = null)
    {
        return !empty($_POST[$key]) ? true : false;
    }

    public static function value($key)
    {
        if (static::has($key)) {
            return trim(filter_var($_POST[$key], FILTER_SANITIZE_STRING));
        } else {
            return false;
        }
    }
}