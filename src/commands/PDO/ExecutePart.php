<?php

namespace connpool\commands\PDO;

use connpool\commands\BasePart;

class ExecutePart extends BasePart
{
    public $depend = PdoPart::class;

    /**
     * @param          $request
     * @param \Closure $next
     *
     * @return \PDOStatement
     * @throws \Exception
     */
    public function handle($request, \Closure $next): \PDOStatement
    {
        $params = $this->getParams($request);

        /** @var \PDOStatement $sth */
        $sth = $next($request);

        $sth->execute($params['input_parameters'] ?? null);

        return $sth;
    }
}
