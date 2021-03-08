<?php

namespace Web\MVC;

class View
{
    public function render($view, $data = [])
    {
        require_once "{$view}.php";
    }
}