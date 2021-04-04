<?php

namespace Web\Database;

use Web\Database\Connection;
use Web\Database\QueryBuilder;
use Web\Database\Interfaces\ModelInterface;

abstract class Model extends QueryBuilder implements ModelInterface
{
    protected static $factory = null;

    public static function factory()
    {
        if (!isset(self::$factory[static::class])) {
            $model = static::class;
            self::$factory[static::class] = new $model(Connection::factory());
        }

        return self::$factory[static::class];
    }

    public function __construct()
    {
        parent::__construct(Connection::factory());
    } 
}