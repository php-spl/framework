<?php

namespace Web\Database;

use Web\Database\QueryBuilder;

class Model extends QueryBuilder
{
    public function __construct(Connection $db)
    {
        parent::__construct($db);
    } 
}