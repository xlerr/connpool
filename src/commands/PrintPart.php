<?php

namespace connpool\commands;

class PrintPart extends BasePart implements PartInterface
{
    /**
     * @param          $params
     * @param \Closure $next
     *
     * @return string
     */
    public function handle($params, \Closure $next): string
    {
        $response = $next($params);

        if (is_array($response)) {
            $response = json_encode($response);
        } elseif (is_object($response)) {
            $response = serialize($response);
        }

        return (string)$response;
    }
}
