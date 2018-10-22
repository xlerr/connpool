<?php

namespace connpool\commands;

use connpool\Core;
use Workerman\Connection\TcpConnection;

abstract class BasePart implements PartInterface
{
    public $depend;

    protected $core;
    protected $connection;

    public function __construct(Core $core, TcpConnection $connection)
    {
        $this->core       = $core;
        $this->connection = $connection;
    }

    abstract public function handle($params, \Closure $next);
}
