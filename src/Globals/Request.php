<?php

namespace Spl\Globals;

use Spl\Globals\Server;
use Spl\Globals\Get;
use Spl\Globals\Post;
use Spl\Globals\File;
use Spl\Globals\Session;
use Spl\Globals\Cookie;

class Request
{
    protected $server;
    protected $cookie;
    protected $session;
    protected $files;
    protected $post;
    protected $get;

    public function __construct(Server $server, Session $session, Cookie $cookie, File $files, Post $post, Get $get)
    {
        $this->server = $server;
        $this->session = $session;
        $this->cookie = $cookie;
        $this->files = $files;
        $this->post = $post;
        $this->get = $get;
    }

    public function exists($type = 'post', $name = null) {
        switch ($type) {
            case 'post':
                return $this->post->exists($name);
                break;
            case 'get':
                return $this->get->exists($name);
                break;
            case 'cookie':
                return $this->cookie->exists($name);
                break;
            case 'session':
                return $this->session->exists($name);
                break;
            case 'files':
                return $this->files->exists($name);
                break;
            default:
                return false;
                break;
        }
    }

    public function has($type, $name) 
    {
        return $this->exists($type, $name);
    }

    public function get($name, $info = null)
    {
        if ($this->post->has($name)) {
            return $this->post->value($name);
        } elseif ($this->get->has($name)) {
            return $this->get->value($name);
        } elseif ($this->cookie->has($name)) {
            return $this->cookie->get($name);
        } elseif ($this->files->has($name, $info)) {
            return $this->files->get($name, $info);
        } else {
            return false;
        }
    }

    public function input($item, $info = null)
    {
        return $this->get($item, $info);
    }

    public function old($item, $info = null)
    {
        return $this->get($item, $info);
    }

    public function all()
    {
        return array_merge(
            $this->get->all(),
            $this->post->all(),
            $this->cookie->all(),
            $this->session->all(),
            $this->files->all()
        );
    }

    public function server()
    {
        return $this->server;
    }

    public function session()
    {
        return $this->session;
    }

    public function cookie()
    {
        return $this->cookie;
    }

    public function files()
    {
        return $this->files;
    }

    public function method()
    {
        return $this->server->method();
    }

    public function time()
    {
        return $this->server->time();
    }

    public function scheme()
    {
        return $this->server->scheme();
    }

    public function query()
    {
        return $this->server->query();
    }

    public function uri()
    {
        return $this->server->uri();
    }
}