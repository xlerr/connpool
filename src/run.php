<?php

include __DIR__ . '/../vendor/autoload.php';

use connpool\Core;
use Workerman\Worker;


// 注意：这里与上个例子不同，使用的是websocket协议
$worker = new Worker("tcp://127.0.0.1:1085");

// 启动4个进程对外提供服务
$worker->count = 4;

$worker->onWorkerStart = function ($worker) {
    $core = new Core();

    $worker->onConnect = [$core, 'onConnect'];
    $worker->onMessage = [$core, 'onMessage'];
    $worker->onClose   = [$core, 'onClose'];
};

// 运行worker
Worker::runAll();