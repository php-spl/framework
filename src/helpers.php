<?php

use Spl\DI\Proxy;
use Spl\Types\Arr;
use Spl\Types\Str;

if (! function_exists('app')) {
    // App
    function app($service = null) {
        $app = Proxy::getProxyApplication();
        if($service) {
            if($app->has($service)) {
                return $app->get($service);
            } else {
                return false;
            }
        }
        return $app;
   }
}

if (! function_exists('config')) {
    // Config
    function config($path) {
        if($path) {
            return app('config')->get($path);
        }
    
    }
}

if (! function_exists('session')) {
    // Session
    function session($name = null) {
        if($name) {
          if(app('session')->has($name)) {
           return app('session')->get($name);
          } else {
            return false;
          }
        }
       return app('session');
    }
}

if (! function_exists('env')) {
   
    function env($key, $default = null) {

        $data = $_ENV;
        $segments = explode('_', $key);
        
        foreach($segments as $segment) {
          if(isset($data[$segment])) {
            $data = $data[$segment];
          } else {
            $data = $default;
            break;
          }
        }
        
        return $data;
    }
}

if (! function_exists('auth')) {
    // Auth
    function auth($guard = null) {
        return app('auth');
    } 
}

if (! function_exists('dump')) {

    function dump() {
        return var_dump(func_get_args());
    }  
}

if (! function_exists('url')) {

    function url($path = '') {
        echo request()->url($path);
    }
}

if (! function_exists('dd')) {

    function dd() {
        return die(func_get_args());
    } 
}

if (! function_exists('halt')) {

    function halt() {
        return var_dump(func_get_args());
        exit();
    } 
}

if (! function_exists('router')) {
    // Router
    function router() {
        return app('router');
    }
}

if (! function_exists('e')) {

    function e($string, $raw = false) {
        if(!$raw) {
          echo htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        } else {
          echo $string;
        }
       
    }
}

if (! function_exists('__')) {
    // Translator
    function __($string, $replace = [], $locale = false) {
        if($locale) {
          app('translator')->forceLanguage($locale);
        }
        echo app('translator')->get($string, $replace);
    }
}

if (! function_exists('route')) {

    function route($name, $params = []) {
        echo router()->link($name, $params);
    }
}

if (! function_exists('view')) {
    // View
    function view($path, $data = []) {
        return app('view')->render($path, $data);
    }
}

if (! function_exists('token')) {
    // Token
    function token() {
        if(app('token')) {
          app('token')->create();
          echo app('token')->csrf();
        }
      
    }
}

if (! function_exists('validate')) {
    // Validator
    function validate($rules) {
        return app('validator')->validate($rules);
    }
}

if (! function_exists('request')) {
    // Request
    function request($key = false) {
        if(!$key) {
         return app('request');
        }
        return app('request')->get($key);
    }
}

if (! function_exists('response')) {
    // Response
    function response() {
        return app('response');
    }
}

if (! function_exists('data_fill')) {
    /**
     * Fill in data where it's missing.
     *
     * @param  mixed  $target
     * @param  string|array  $key
     * @param  mixed  $value
     * @return mixed
     */
    function data_fill(&$target, $key, $value)
    {
        return data_set($target, $key, $value, false);
    }
}

if (! function_exists('data_get')) {
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed  $target
     * @param  string|array|int|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    function data_get($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        foreach ($key as $i => $segment) {
            unset($key[$i]);

            if (is_null($segment)) {
                return $target;
            }

            if ($segment === '*') {
                if (! is_array($target)) {
                    return value($default);
                }

                $result = [];

                foreach ($target as $item) {
                    $result[] = data_get($item, $key);
                }

                return in_array('*', $key) ? Arr::collapse($result) : $result;
            }

            if (Arr::accessible($target) && Arr::exists($target, $segment)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }
}

if (! function_exists('data_set')) {
    /**
     * Set an item on an array or object using dot notation.
     *
     * @param  mixed  $target
     * @param  string|array  $key
     * @param  mixed  $value
     * @param  bool  $overwrite
     * @return mixed
     */
    function data_set(&$target, $key, $value, $overwrite = true)
    {
        $segments = is_array($key) ? $key : explode('.', $key);

        if (($segment = array_shift($segments)) === '*') {
            if (! Arr::accessible($target)) {
                $target = [];
            }

            if ($segments) {
                foreach ($target as &$inner) {
                    data_set($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (Arr::accessible($target)) {
            if ($segments) {
                if (! Arr::exists($target, $segment)) {
                    $target[$segment] = [];
                }

                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || ! Arr::exists($target, $segment)) {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments) {
                if (! isset($target->{$segment})) {
                    $target->{$segment} = [];
                }

                data_set($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || ! isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = [];

            if ($segments) {
                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }

        return $target;
    }
}

if (! function_exists('head')) {
    /**
     * Get the first element of an array. Useful for method chaining.
     *
     * @param  array  $array
     * @return mixed
     */
    function head($array)
    {
        return reset($array);
    }
}

if (! function_exists('last')) {
    /**
     * Get the last element from an array.
     *
     * @param  array  $array
     * @return mixed
     */
    function last($array)
    {
        return end($array);
    }
}

if (! function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value, ...$args)
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }
}