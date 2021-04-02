<?php

namespace Web\Database;

use Web\Database\QueryBuilder;

class Model extends QueryBuilder implements ModelInterface
{
    public function __construct(Connection $db)
    {
        parent::__construct($db);
    } 
}