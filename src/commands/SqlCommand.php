<?php

namespace connpool\commands;

use connpool\lib\PDO;

class SqlCommand extends Command
{
    public function run(string $db, string $sql, array $params = [], string $func = 'rowCount')
    {
        $conn = $this->core->buildDb($this->connection, $db);
        var_dump($conn);

        $sth = $conn->build($sql, $params);

        return call_user_func_array([$sth, $func], $this->buildParams($func));
    }

    private function buildParams(string $func): array
    {
        switch ($func) {
            case 'fetchAll':
            case 'fetch':
                return [PDO::FETCH_ASSOC];
            default:
                return [];
        }
    }
}