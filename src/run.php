<?php

include __DIR__ . '/../vendor/autoload.php';

use connpool\Kernel;
use Workerman\Worker;

defined('DEBUG') or define('DEBUG', false);
defined('ROOT_PATH') or define('ROOT_PATH', __DIR__);

// 注意：这里与上个例子不同，使用的是websocket协议
$worker = new Worker("tcp://127.0.0.1:1085");

// 启动4个进程对外提供服务
$worker->count = 1;

$worker->onWorkerStart = function ($worker) {
    $core = new Kernel();

    $worker->onConnect = [$core, 'onConnect'];
    $worker->onMessage = [$core, 'onMessage'];
    $worker->onClose   = [$core, 'onClose'];
};

// 运行worker
Worker::runAll();