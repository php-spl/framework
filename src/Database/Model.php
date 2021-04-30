<?php

namespace Spl\Database;

use PDO;
use Spl\Database\Connection;
use Spl\Database\Query;

class Model extends Query
{
    protected static $instance = null;

    public static function singleton()
    {
        if (!isset(self::$instance[static::class])) {
            $model = static::class;
            self::$instance[static::class] = new $model(Connection::singleton());
        }

        return self::$instance[static::class];
    }

    public function __construct(PDO $db)
    {
        parent::__construct($db);
    } 
}