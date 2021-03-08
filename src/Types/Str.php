<?php

namespace Web\Types;

class Str
{
    public static function escape($string)
    {
        return trim(filter_var($string, FILTER_SANITIZE_STRING));
    }

    public static function strip($string, $tags = '')
    {
        return strip_tags($string, $tags);
    }

    public static function serialize($input)
    {
        return serialize($input);
    }

    public static function unserialize($input)
    {
        return unserialize($input);
    }

    public static function jsonEncode($input)
    {
        return json_encode($input);
    }

    public static function jsonDecode($json)
    {
        return json_decode($json);
    }

    // This function expects the input to be UTF-8 encoded.
    public static function toSlug($string, $replace = array(), $delimiter = '-')
    {
        if (!empty($replace)) {
            $string = str_replace((array) $replace, ' ', $string);
        }

        return preg_replace('/[^A-Za-z0-9-]+/', $delimiter, $string);
    }

    /*
    * Create a random  string
    */
    public static function rand($length)
    {
        $chars = '~)!abc}def#ghijkl[m-no.pqrs]tu;v|wx+yzA%BC(D:EF{GHI&JKLM=NOP*QRS?TU4567VWXYZ_012389' .uniqid();
        $str   =  '';
        $size  = strlen($chars);

        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $size - 1)];
        }
        
        return $str;
    }

}