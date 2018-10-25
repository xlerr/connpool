<?php

namespace connpool\commands\PDO;

use connpool\commands\BasePart;

/**
 * Class FetchAllPart
 *
 * $request = [
 *     'command' => 'fetchAll',
 *     'Db' => [
 *         'name' => 'biz',
 *     ],
 *     'Pdo' => [
 *         '__function' => 'prepare',
 *         'statement' => 'select * from asset_extend where asset_extend_id > :id order by asset_extend_id asc limit 1',
 *         'options' => [],
 *     ],
 *     'Execute' => [
 *         'input_parameters' => [
 *             'id' => 6050,
 *         ],
 *     ],
 *     'FetchAll' => [
 *         'fetch_style' => \PDO::FETCH_ASSOC,
 *         'fetch_argument' => null,
 *         'ctor_args' => [],
 *     ],
 * ];
 *
 * @package connpool\commands\PDO
 */
class FetchAllPart extends BasePart
{
    public $depend = ExecutePart::class;

    /**
     *
     * @param          $request
     * @param \Closure $next
     *
     * @return array|mixed
     * @throws \Exception
     */
    public function handle($request, \Closure $next)
    {
        $params = $this->getParams($request);

        /** @var \PDOStatement $sth */
        $sth = $next($request);

        $fetchStyle = $params['fetch_style'] ?? null;

        return [
            'data' => $sth->fetchAll($fetchStyle),
        ];
    }
}
