<?php

namespace Web\Env;

class Config
{
    protected $data;

    protected $default;

    public function __construct($file = null)
    {
        if($file) {
            $this->load($file);
        }
    }

    public function load($file)
    {
        $this->data = require $file;
    }

    public function get($key, $default = null)
    {
        $this->default = $default;

        $segments = explode('.', $key);
        $data = $this->data;

        foreach($segments as $segment) {
            if(isset($data[$segment])) {
                $data = $data[$segment];
            } else {
                $data = $this->default;
                break;
            }
        }

        return $data;
    }

    public function __get($property) 
    {
        if ($this->get($property)) {
            return $this->get($property);
        }
    }
}