<?php

namespace connpool\commands;

interface PartInterface
{
    /**
     * @param          $params
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($params, \Closure $next);
}