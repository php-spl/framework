<?php

namespace Web\Types;

/**
 * Integer class
 */

class Intr
{
    public static function is($var)
    {
        if(is_int($var) || is_numeric($var) || is_integer($var) || is_float($var)) {
            return true;
        } else {
            return false;
        }
    }

}