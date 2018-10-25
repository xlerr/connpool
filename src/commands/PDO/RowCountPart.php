<?php

namespace connpool\commands\PDO;

use connpool\commands\BasePart;
use connpool\commands\PartInterface;

class RowCountPart extends BasePart implements PartInterface
{
    public $depend = ExecutePart::class;

    /**
     * @param          $request
     * @param \Closure $next
     *
     * @return int
     */
    public function handle($request, \Closure $next): int
    {

        /** @var \PDOStatement $sth */
        $sth = $next($request);

        return $sth->rowCount();
    }
}
