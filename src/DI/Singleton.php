<?php

namespace Classes;

abstract class Singleton
{
    /**
     * The instance being registered to the singleton.
     * 
     */
    protected static $instance;

    public static function singleton()
    {
        if (!isset(self::$instance[static::class])) {
            $class = static::class;
            self::$instance[static::class] = new $class();
        }

        return self::$instance[static::class];
    }
}