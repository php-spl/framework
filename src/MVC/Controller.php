<?php

namespace Web\MVC;

use Web\MVC\Interfaces\ControllerInterface;
use Exception;

abstract class Controller implements ControllerInterface
{
    protected $app;

    public function __construct($container)
    {
        $this->app = $container;

        if($this->app->has('csrf')) {
            if($this->csrf->requestExists()) {
                if(!$this->csrf->verify()) {
                    throw new Exception("CSRF error!");
                }
            } else {
                $this->csrf->setToken();
            }
        }
    }

    public function __get($property) 
    {
        if ($this->app->get($property)) {
            return $this->app->get($property);
        }
    }

    public function index()
    {

    }

    public function create()
    {

    }

    public function store()
    {

    }

    public function show($id)
    {

    }

    public function edit($id)
    {

    }

    public function update($id)
    {

    }

    public function destroy($id)
    {

    }

    public function delete()
    {
        
    }

}