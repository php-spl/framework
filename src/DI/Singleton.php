<?php

namespace Spl\DI;

abstract class Singleton
{
    /**
     * The instance being registered to the singleton.
     * 
     */
    protected static $instance;

    public static function singleton(...$args)
    {
        if (!isset(self::$instance[static::class])) {
            $class = static::class;
            self::$instance[static::class] = new $class(...$args);
        }

        return self::$instance[static::class];
    }
}