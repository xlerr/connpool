<?php

namespace connpool\commands\PDO;

use connpool\commands\BasePart;
use connpool\commands\PartInterface;
use connpool\lib\PDO;

class FetchAllPart extends BasePart implements PartInterface
{
    public $depend = ExecutePart::class;

    public function handle($request, \Closure $next)
    {
        $sth = $next($request);

        $records = $sth->fetchAll($request['fetch'] ?? PDO::FETCH_ASSOC);

        return $records;
    }
}
