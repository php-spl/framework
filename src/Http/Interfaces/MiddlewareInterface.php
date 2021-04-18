<?php

namespace Web\Http\Interfaces;

use Closure;

interface MiddlewareInterface 
{

    public function process($object, Closure $next);

}