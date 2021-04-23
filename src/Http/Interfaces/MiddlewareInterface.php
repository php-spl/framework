<?php

namespace Spl\Http\Interfaces;

use Closure;

interface MiddlewareInterface 
{

    public function handle($object, Closure $next);

}