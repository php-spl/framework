<?php

namespace Web\Types;

/**
 * Object class
 */

class Obj
{
    public static $object;

    public static function set(string $key, array $functions)
    {
        self::$object[$key] = (object)[$functions];
    }

    public static function get(string $key)
    {
        if(isset(self::$object[$key])) {
            return self::$object[$key];
        } else {
            return false;
        }
    }


}