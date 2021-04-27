<?php

namespace Spl\Globals;

class Env 
{
    public function set($name, $value)
    {
        $_ENV[$name] = $value;
    }

    public function get($path = null) 
    {
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