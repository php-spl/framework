<?php

namespace Web\MVC;

use Web\DI\Container;

class App
{
    public $request;

    protected $container;

    protected $path;
    protected $queryString;

    protected $namespace;
    protected $controller;
    protected $action;
    protected $params = array();
 

    public function __construct(Container $container) 
    {
        $this->container = $container;

        $this->path = '';
        $this->controller = 'default';
        $this->action = 'index';
        $this->queryString = 'route';
        $this->namespace = 'App\Controllers';
    }

    /**
     * Set path to controllers
     * @param type $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Set default controller
     *
     * @param type $name
     */
    public function setController($name)
    {
        $this->controller = $name;
    }

    /**
     * Set default action
     *
     * @param type $name
     */
    public function setAction($name)
    {
        $this->action = $name;
    }

    /**
     * Set default query string from $GET
     *
     * @param type $name
     */
    public function setQueryString($name)
    {
        $this->queryString = $name;
    }
    
    /**
     * Set default namespace for psr-2 autoloading
     *
     * @param type $name
     */
    public function setNamespace($name)
    {
        $this->namespace = $name;
    }

    /**
     * Fetching the controller class and its methods
     * happens in the contructer doing every run.
     * Default params are:
     * array('controller' =>'default','action'=>'index','pathcontrollers' => '', 'rooturi'=> 'url')
     * $object can be something to pass to the controllers constructer
     */
    public function run()
    {
        // use this class method to parse the $GET[url]
        $this->request = $this->router($this->queryString);

        if (!empty($this->request)) {
            $this->controller = ucfirst($this->request[0]);
        } else {
            $this->request = array($this->controller, $this->action);
        }

        // checks if a controller by the name from the URL exists
        if (str_replace('', '', $this->request[0]) &&
            file_exists($this->path . ucfirst($this->controller) . 'Controller.php')) {

            // if exists, use this as the controller instead of default
            $this->controller = ucfirst($this->controller) . 'Controller';

            /*
             * destroys the first URL parameter,
             *  to leave it like index.php?url=[0]/[1]/[parameters in array seperated by "/"]
             */
            unset($this->request[0]);
        } else {
            return header("HTTP/1.0 404 Not Found");
        }

        // initiate the controller class as an new object
        $controller = "{$this->namespace}\\" . $this->controller;
    
        $this->controller = new $controller($this->container);

        // checks for if a second url parameter like index.php?url=[0]/[1] is set
        if (!empty($this->request)) {

            // then check if an according method exists in the controller from $url[0]
            if (method_exists($this->controller, $this->request[1])) {

                // if exists, use this as the method instead of default
                $this->action = $this->request[1];

                /*
                 * destroys the second URL, to leave only the parameters
                 *  left like like index.php?url=[parameters in array seperated by "/"]
                 */
                unset($this->request[1]);

            } else {
                return header("HTTP/1.0 404 Not Found");
            }
        }

        /**
         * checks if the $GET['url'] has any parameters left in the
         * index.php?url=[parameters in array seperated by "/"].
         * If it has, get all the values. Else, just parse is as an empty array.
         */
        $this->params = $this->request ? array_values($this->request) : array();

        /**
         * 1. call/execute the controller and it's method.
         * 2. If the Router has NOT changed them, use the default controller and method.
         * 2. if there are any params, return these too. Else just return an empty array.
         */
        call_user_func_array(array($this->controller, $this->action), $this->params);
    }


    /**
     * The router method is responsible for getting the $_GET-parameters
     * as an array, for sanitizing it for anything we don't want and removing "/"-slashes
     * after the URL-parameter
     */
    protected function router($queryString) 
    {
        if (isset($_GET[$queryString])) {
            return explode('/', filter_var(rtrim($_GET[$queryString], '/'), FILTER_SANITIZE_URL));
        }
    }

}