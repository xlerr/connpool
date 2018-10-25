<?php

namespace connpool\commands\PDO;

use connpool\commands\BasePart;
use connpool\commands\PartInterface;

class PreparePart extends BasePart implements PartInterface
{
    public $depend = DbPart::class;

    /**
     * @param          $request
     * @param \Closure $next
     *
     * @return \PDOStatement
     * @throws \Exception
     */
    public function handle($request, \Closure $next): \PDOStatement
    {
        $sql = $request['sql'] ?? null;
        if (empty($sql)) {
            throw new \Exception('缺少SQL语句');
        }

        /** @var \connpool\lib\Connection $conn */
        $conn = $next($request);

        return $conn->db->prepare($sql);
    }
}
