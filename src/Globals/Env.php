<?php

namespace Spl\Globals;

class Env 
{
    public static function get($path = null) {
        if ($path) {
            $config = $_ENV;
            $path = explode('.', $path);

            foreach ($path as $bit) {
                if (isset($config[$bit])) {
                    $config = $config[$bit];
                }
            }
            return $config;
        }
        return false;
    }

}