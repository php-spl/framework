<?php

namespace Web\Database;

use Web\Database\QueryBuilder;
use Web\Database\Interfaces\ModelInterface;

class Model extends QueryBuilder implements ModelInterface
{
    public function __construct(Connection $db)
    {
        parent::__construct($db);
    } 
}