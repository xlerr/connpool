<?php

namespace connpool;

use connpool\lib\PDO;

class Pool
{
    /**
     * @var pool name
     */
    public $name;

    /**
     * @var int Minimum number of connections
     */
    public $min;

    /**
     * @var int Maximum number of connections
     */
    public $max;

    /**
     * @var array Connection container
     */
    protected $container = [];

    protected $used = [];

    /**
     * @var \connpool\Config Connection configuration
     */
    private $config;

    /**
     * Pool constructor.
     *
     * @param string           $name
     * @param \connpool\Config $config
     */
    public function __construct(string $name, Config $config)
    {
        $this->name   = 'pool:' . ($name ?? uniqid());
        $this->min    = (int)$config->get('min', 1);
        $this->max    = (int)$config->get('max', 1);
        $this->config = new Config($config->get('db', []));
    }

    /**
     * Initialize the connection container
     */
    protected function init()
    {
        for ($i = 0; $i < $this->min; $i++) {
            $this->container[] = $this->newInstance();
        }
    }

    /**
     * @return \connpool\lib\PDO|null
     */
    protected function newInstance()
    {
        $dsn      = $this->config->get('dsn');
        $username = $this->config->get('username');
        $password = $this->config->get('password');
        $options  = $this->config->get('options', []);

        try {
            return new PDO($dsn, $username, $password, $options);
        } catch (\PDOException $e) {
            throw $e;

            // todo write log
            return null;
        }
    }

    /**
     * pop up connection
     *
     * @return \connpool\lib\PDO
     */
    public function popup(): PDO
    {
        while (1) {
            $instance = array_shift($this->container);
            if (!$instance) {
                if (count($this->container) + count($this->used) < $this->max) {
                    $instance = $this->newInstance();
                } else {
                    usleep(100);
                    continue;
                }
            }

            $this->used[] = $instance;

            return $instance;
        }
    }

    /**
     * recover connection
     *
     * @param \connpool\lib\PDO $instance
     */
    public function recover(PDO $instance): void
    {
        if (false !== ($index = array_search($instance, $this->used))) {
            $this->container[] = $instance;
            unset($this->used[$index]);
        }
        var_dump($this->container, $this->used);
    }
}