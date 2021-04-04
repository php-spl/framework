<?php

namespace Web\Http;

use Web\Http\Interfaces\ControllerInterface;
use Exception;

class Controller implements ControllerInterface
{
    /**
     * @var array $params
     */
    public static $params = [];
    
    /**
    * @var array Before Middlewares
    */
    public $middlewareBefore = [
       
    ];

    /**
    * @var array After Middlewares
    */
    public $middlewareAfter = [
        
    ];



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