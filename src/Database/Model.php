<?php

namespace Web\Database;

use Web\Database\QueryBuilder;

class Model extends QueryBuilder
{
    public function __construct(Database $db)
    {
        parent::__construct($db);
    } 
}