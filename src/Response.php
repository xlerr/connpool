<?php

namespace connpool;

class Response
{
    protected $body;

    public function __construct($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        $body = $this->body;
        if (is_array($body)) {
            $body += ['error' => 0];
        } else {
            $body = ['error' => 0, 'data' => $body];
        }

        return json_encode($body);
    }

    public function __toString()
    {
        return $this->toString();
    }
}