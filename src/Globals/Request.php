<?php

namespace Spl\Globals;

use Spl\Globals\Server;

class Request
{
    public static function has($type = 'post') {
        switch ($type) {
            case 'post':
                return (!empty($_POST)) ? true : false;
                break;
            case 'get':
                return (!empty($_GET)) ? true : false;
                break;
            case 'cookie':
                return (!empty($_COOKIE)) ? true : false;
                break;
            case 'files':
                return (!empty($_FILES)) ? true : false;
                break;
            default:
                return false;
                break;
        }
    }

    public static function get($key, $info = null)
    {
        if (isset($_POST[$key])) {
            return trim(filter_var($_POST[$key], FILTER_SANITIZE_STRING));
        } elseif (isset($_GET[$key])) {
            return trim(filter_var($_GET[$key], FILTER_SANITIZE_STRING));
        } elseif (isset($_COOKIE[$key])) {
            return trim(filter_var($_GET[$key], FILTER_SANITIZE_STRING));
        } elseif (isset($_FILES[$key][$info])) {
            return $_FILES[$key][$info];
        } else {
            return false;
        }
    }

    public static function method()
    {
        return Server::method();
    }

    public static function time()
    {
        return Server::time();
    }

    public static function scheme()
    {
        return Server::scheme();
    }

    public static function query()
    {
        return Server::query();
    }

    public static function uri()
    {
        return Server::uri();
    }
}