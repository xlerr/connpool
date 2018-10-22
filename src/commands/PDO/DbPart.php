<?php

namespace connpool\commands\PDO;

use connpool\commands\BasePart;
use connpool\commands\PartInterface;
use connpool\lib\Connection;

class DbPart extends BasePart implements PartInterface
{
    public function handle($request, \Closure $next): Connection
    {
        $database = $request['database'] ?? null;
        if (empty($database)) {
            throw new \Exception('缺少库名');
        }

        return $this->core->buildDb($this->connection, $database);
    }
}
