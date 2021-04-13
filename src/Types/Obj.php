<?php

namespace Web\Types;

/**
 * Object class
 */

class Obj
{
    public static $object;

    public static function set($key, $functions)
    {
        self::$object[$key] = (object)[$functions];
    }

    public static function get($key)
    {
        if(isset(self::$object[$key])) {
            return self::$object[$key];
        } else {
            return false;
        }
    }


}