<?php

namespace connpool\commands\PDO;

use connpool\commands\BasePart;

class PdoPart extends BasePart
{
    public $depend = DbPart::class;

    /**
     * @param          $request
     * @param \Closure $next
     *
     * @return mixed
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function handle($request, \Closure $next)
    {
        $params = $this->getParams($request);

        $func = $params['__function'] ?? null;
        if (empty($func)) {
            throw new \Exception('方法不存在: PDO::' . $func);
        }

        /** @var \connpool\lib\PDO $db */
        $db = $next($request);
        var_dump($db);

        $rm         = new \ReflectionMethod($db, $func);
        $parameters = $rm->getParameters();
        $args       = [];
        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            if (!isset($params[$name])) {
                if (!$parameter->isDefaultValueAvailable()) {
                    throw new \ReflectionException('缺少参数:' . $name);
                }
                $val = $parameter->getDefaultValue();
            } else {
                $val = $params[$name];
            }

            $args[$parameter->getPosition()] = $val;
        }

        ksort($args);

        return $rm->invokeArgs($db, $args);
    }
}
