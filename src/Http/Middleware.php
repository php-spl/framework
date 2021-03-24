<?php

namespace Web\Http;

use Exception;

abstract class Middleware
{
    public static function handle()
    {
        return true;   
    }

}