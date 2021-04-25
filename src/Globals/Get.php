<?php

namespace Spl\Globals;

class Get
{
    public static function has($key = null)
    {
        return !empty($_GET[$key]) ? true : false;
    }

    public static function value($key)
    {
        if (static::has($key)) {
            return trim(filter_var($_GET[$key], FILTER_SANITIZE_STRING));
        } else {
            return false;
        }
    }
}