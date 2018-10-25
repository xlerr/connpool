<?php

namespace connpool;

use Workerman\Connection\TcpConnection;

class Kernel
{
    /**
     * @var \connpool\PoolManager
     */
    public $poolManager;

    public function __construct()
    {
        $configFilepath = __DIR__ . '/config/main.php';

        $this->poolManager = new PoolManager($configFilepath);
    }

    public function onConnect(TcpConnection $connection)
    {
        echo sprintf("#%d connected\n", $connection->id);
    }

    public function onMessage(TcpConnection $connection, string $data)
    {
        $params = json_decode($data, true);
        if (!is_array($params)) {
            $params = [];
        }

        $response = Handler::make($this, $connection, $params);

        $connection->send($response->toString());
    }

    /**
     * @param \Workerman\Connection\TcpConnection $connection
     *
     * @throws \Exception
     */
    public function onClose(TcpConnection $connection)
    {
        $connId = $connection->id;
        $this->poolManager->recover($connId);

        echo sprintf("#%d closed\n", $connId);
    }
}