<?php

namespace connpool\commands;

class Response
{
    protected $body;

    public static $commands = [
        'fetchAll' => FetchAllPart::class,
        'print'    => PrintPart::class,
    ];

    public static function make(Core $core, TcpConnection $connection, array $params)
    {
        if (isset($params['command']) && isset(self::$commands[$params['command']])) {
            $class = self::$commands[$params['command']];
            unset($params['command']);
        } else {
            $class = PrintPart::class;
        }

        $stack = [];
        while (1) {
            $instance = new $class($core, $connection);
            array_push($stack, $instance);
            if (property_exists($instance, 'depend') && !empty($instance->depend) && class_exists($instance->depend)) {
                $class = $instance->depend;
            } else {
                break;
            }
        }

        $response = (new Pipeline())
            ->send($params)
            ->through($stack)
            ->then(function ($request) {
                return $request;
            });

        return new static($response);
    }

    public function __construct($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        switch (gettype($this->body)) {
            case 'array':
                $content = json_encode($this->body);
                break;
            default:
                $content = $this->body;
        }

        return (string)$content;
    }

    public function __toString()
    {
        return $this->toString();
    }
}