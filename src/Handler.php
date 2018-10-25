<?php

namespace connpool;

use connpool\commands\PDO\ExecutePart;
use connpool\commands\PDO\FetchAllPart;
use connpool\commands\PDO\RowCountPart;
use connpool\commands\PrintPart;
use connpool\lib\Pipeline;
use Workerman\Connection\TcpConnection;

class Handler
{
    public static $commands = [
        'fetchAll' => FetchAllPart::class,
        'execute'  => ExecutePart::class,
        'rowCount' => RowCountPart::class,
        'print'    => PrintPart::class,
    ];

    public static function make(Kernel $kernel, TcpConnection $connection, array $params)
    {
        if (isset($params['command']) && isset(self::$commands[$params['command']])) {
            $class = self::$commands[$params['command']];
            unset($params['command']);
        } else {
            $class = PrintPart::class;
        }

        $stack = [];
        while (1) {
            $instance = new $class($kernel, $connection);
            array_push($stack, $instance);
            if (property_exists($instance, 'depend') && !empty($instance->depend)) {
                $class = $instance->depend;
            } else {
                break;
            }
        }

        $result = (new Pipeline())
            ->send($params)
            ->through($stack)
            ->then(function ($request) {
                return $request;
            });

        return new Response($result);
    }
}