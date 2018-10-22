<?php

namespace connpool;

use connpool\commands\Command;
use connpool\commands\Pipeline;
use connpool\commands\Response;
use connpool\lib\Connection;
use connpool\lib\PDO;
use Workerman\Connection\TcpConnection;

class Core
{
    protected $max    = 10;
    protected $config = [
        'biz'     => [
            'dsn'      => 'mysql:host=127.0.0.1;port=33327;dbname=qsq_erp',
            'username' => 'cdbiz',
            'password' => '0de62b3bd6ee03e',
            'charset'  => 'utf8',
            'option'   => [
                PDO::ATTR_PERSISTENT => true,
            ],
        ],
        'capital' => [
            'dsn'      => 'mysql:host=127.0.0.1;dbname=capital',
            'username' => 'root',
            'password' => 'root',
            'charset'  => 'utf8',
            'option'   => [
                PDO::ATTR_PERSISTENT => true,
            ],
        ],
    ];
    protected $pools  = [];
    protected $links  = [];

    public function buildDb(TcpConnection $connection, string $name)
    {
        $instance = null;
        if (isset($this->pools[$name])) {
            $instance = array_shift($this->pools[$name]);
        }

        if (!$instance) {
            $instance = new Connection($name, $this->config[$name]);
        }

        $this->links[$connection->id][$name] = $instance;

        return $instance;
    }

    public function onConnect(TcpConnection $connection)
    {
        echo sprintf("#%d connected\n", $connection->id);
    }

    public function onMessage(TcpConnection $connection, string $data)
    {
        $response = Response::make($this, $connection, json_encode($data, true) ?? []);

        $connection->send($response->toString());
    }

    public function onClose(TcpConnection $connection)
    {
        $connId = $connection->id;

        if (isset($this->links[$connId])) {
            foreach ($this->links[$connId] as $key => $instance) {
                $this->pools[$key][] = $instance;
            }
            unset($this->pools[$connId]);
        }

        echo sprintf("#%d closed\n", $connId);
    }
}