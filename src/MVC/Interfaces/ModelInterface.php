<?php

namespace web\MVC\Interfaces;

interface ModelInterface
{
    public function create($fields);

    public function read($where);

    public function update($fields, $where);

    public function delete($where);
}