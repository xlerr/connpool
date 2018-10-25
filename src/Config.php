<?php

namespace connpool;

class Config
{
    private $data = [];

    /**
     * Config constructor.
     *
     * @param string $path
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return $this->data[$key];
        } else {
            return $default;
        }
    }

    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    public function __get($name)
    {
        return $this->get($name);
    }
}