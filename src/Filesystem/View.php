<?php

namespace Spl\Filesystem;

class View
{
    public $path = '';

    public function render($view, $data = [])
    {
        if($data) {
            extract($data);
        }

        require_once "{$this->path}/{$view}.php";
    }

    /**
     * Renders pure JSON to the browser, useful for API construction
     * @param $data
     */
    public function renderJson($data)
    {
        echo json_encode($data);
    }
}