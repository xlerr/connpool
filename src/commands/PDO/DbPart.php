<?php

namespace connpool\commands\PDO;

use connpool\commands\BasePart;
use connpool\lib\PDO;

class DbPart extends BasePart
{
    /**
     * @param          $request
     * @param \Closure $next
     *
     * @return \connpool\lib\PDO
     * @throws \Exception
     */
    public function handle($request, \Closure $next): PDO
    {
        $params = $this->getParams($request);
        $name   = $params['name'] ?? null;
        if (empty($name)) {
            throw new \Exception('缺少库名: name');
        }

        return $this->kernel->poolManager->getConnection($this->connection->id, $name);
    }
}
