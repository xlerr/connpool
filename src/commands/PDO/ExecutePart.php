<?php

namespace connpool\commands\PDO;

use connpool\commands\BasePart;
use connpool\commands\PartInterface;

class ExecutePart extends BasePart implements PartInterface
{
    public $depend = PreparePart::class;

    /**
     * @param          $request
     * @param \Closure $next
     *
     * @return \PDOStatement
     */
    public function handle($request, \Closure $next): \PDOStatement
    {
        $sth = $next($request);

        $sth->execute($request['params'] ?? []);

        return $sth;
    }
}
