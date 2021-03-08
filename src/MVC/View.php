<?php

namespace Web\MVC;

class View
{
    protected $path = '';
    protected $app;

    public function __construct($container)
    {
        $this->app = $container;
    }

    public function __get($property) 
    {
        if ($this->app->get($property)) {
            return $this->app->get($property);
        }
    }

    public function setViewsPath($path)
    {
        $this->path = $path;
    }

    public function render($view, $data = [])
    {
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