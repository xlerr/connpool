<?php

namespace connpool\commands;

use connpool\Kernel;
use Workerman\Connection\TcpConnection;

abstract class BasePart implements PartInterface
{
    public $depend;

    protected $kernel;
    protected $connection;

    public function __construct(Kernel $kernel, TcpConnection $connection)
    {
        $this->kernel     = $kernel;
        $this->connection = $connection;
    }

    abstract public function handle($params, \Closure $next);

    /**
     * @return mixed
     * @throws \Exception
     */
    public static function name()
    {
        if (!preg_match('/([A-Za-z0-9_]+)Part$/', static::class, $match)) {
            throw new \Exception('未知的类名:' . static::class);
        }

        return $match[1];
    }

    /**
     * @param array $request
     * @param mixed $default
     *
     * @return array|mixed
     * @throws \Exception
     */
    public function getParams(array $request, $default = [])
    {
        $name = static::name();
        return $request[$name] ?? $default;
    }
}
