<?php

namespace connpool;

use connpool\lib\PDO;

class PoolManager
{
    protected $pools;

    protected $relation = [];

    public function __construct(string $configFilepath)
    {
        $this->initPools($this->loadConfig($configFilepath));
    }

    protected function loadConfig(string $configFilepath): array
    {
        $configFilepath = \realpath($configFilepath);
        if (!\file_exists($configFilepath)) {
            throw new \Exception('configure file not exists!');
        }

        $config = require $configFilepath;
        if (!\is_array($config) || empty($config)) {
            throw new \Exception('config error!');
        }

        return $config;
    }

    protected function initPools($poolConfig)
    {
        foreach ($poolConfig as $name => $config) {
            $this->pools[$name] = new Pool($name, new Config($config));
        }
    }

    public function has($name)
    {
        return \array_key_exists($name, $this->pools);
    }

    /**
     * @param $name
     *
     * @return \connpool\Pool
     * @throws \Exception
     */
    public function pool($name): Pool
    {
        if (!$this->has($name)) {
            throw new \Exception('unknown pool the ' . $name);
        }

        return $this->pools[$name];
    }

    /**
     * @param $connId
     * @param $key
     *
     * @return \connpool\lib\PDO
     * @throws \Exception
     */
    public function getConnection($connId, $key): PDO
    {
        if (!isset($this->relation[$connId][$key]) || !($this->relation[$connId][$key] instanceof PDO)) {
            $this->relation[$connId][$key] = $this->pool($key)->popup();
        }

        return $this->relation[$connId][$key];
    }

    /**
     * @param $connId
     *
     * @throws \Exception
     */
    public function recover($connId): void
    {
        if (isset($this->relation[$connId])) {
            foreach ($this->relation[$connId] as $key => $instance) {
                $this->pool($key)->recover($instance);
            }
            unset($this->relation[$connId]);
        }
    }
}