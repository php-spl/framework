<?php

namespace Web\Http;

use Web\Http\Interfaces\MiddlewareInterface;
use InvalidArgumentException;
use Closure;

class Middleware 
{
    protected $middlewares;

    public function __construct(array $middlewares = [])
    {
        $this->middlewares = $middlewares;
    }

    /**
     * Add middleware(s) or Middleware
     * @param  mixed $middlewares
     * @return Middleware
     */
    public function add($middlewares)
    {
        if ($middlewares instanceof Middleware) {
            $middlewares = $middlewares->toArray();
        }

        if ($middlewares instanceof MiddlewareInterface) {
            $middlewares = [$middlewares];
        }

        if (!is_array($middlewares)) {
            throw new InvalidArgumentException(get_class($middlewares) . " is not a valid middleware.");
        }

        return new static(array_merge($this->middlewares, $middlewares));
    }

    /**
     * Run middleware around core function and pass an
     * object through it
     * @param  mixed  $object
     * @param  Closure $core
     * @return mixed         
     */
    public function handle($object, Closure $core)
    {
        $coreFunction = $this->createCoreFunction($core);

        // Since we will be "currying" the functions starting with the first
        // in the array, the first function will be "closer" to the core.
        // This also means it will be run last. However, if the reverse the
        // order of the array, the first in the list will be the outer layers.
        $middlewares = array_reverse($this->middlewares);

        // We create the middleware by starting initially with the core and then
        // gradually wrap it in layers. Each layer will have the next layer "curried"
        // into it and will have the current state (the object) passed to it.
        $completeMiddleware = array_reduce($middlewares, function($next, $middleware){
            return $this->createMiddleware($next, $middleware);
        }, $coreFunction);

        // We now have the complete onion and can start passing the object
        // down through the layers.
        return $completeMiddleware($object);
    }

    /**
     * Get the layers of this onion, can be used to merge with another onion
     * @return array
     */
    public function toArray()
    {
        return $this->middlewares;
    }

    /**
     * The inner function of the middleware.
     * This function will be wrapped on layers
     * @param  Closure $core the core function
     * @return Closure
     */
    private function createCoreFunction(Closure $core)
    {
        return function($object) use ($core) {
            return $core($object);
        };
    }

    /**
     * Get an onion layer function.
     * This function will get the object from a previous layer and pass it inwards
     * @param  MiddlewareInterface $next
     * @param  MiddlewareInterface $middleware
     * @return Closure
     */
    private function createMiddleware(Closure $next, $middleware)
    {
        return function($object) use ($next, $middleware){
            return $middleware->handle($object, $next);
        };
    }

}