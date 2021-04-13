<?php

namespace Web\Types;

/**
 * Array class
 */

class Arr
{
    public static function set()
    {
        return func_get_args();
    }

    public static function get(string $key, array $data, $delimiter = '.', $default = null): string
    {
        $segments = explode($delimiter, $key);
        
        foreach($segments as $segment) {
            if(isset($data[$segment])) {
            $data = $data[$segment];
            } else {
            $data = $default;
            break;
            }
        }
        
        return $data;
    }

    public static function is(array $array): bool
    {
        if(is_array($array)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isAssoc(array $var): bool
    {
        return is_array($var) and (array_values($var) !== $var);
    }

}