<?php

namespace connpool\commands;

use connpool\Core;
use Workerman\Connection\TcpConnection;

class Command implements CommandInterface
{
    protected $core;
    protected $connection;
    protected $config;

    public static $commands = [
        'say' => SayCommand::class,
        'sql' => SqlCommand::class,
    ];

    public function __construct(Core $core, TcpConnection $connection, array $config)
    {
        $this->core       = $core;
        $this->connection = $connection;
        $this->config     = $config;
    }

    /**
     * @param \connpool\Core                      $core
     * @param \Workerman\Connection\TcpConnection $connection
     * @param string                              $data
     *
     * @return \connpool\commands\Command
     * @throws \Exception
     */
    public static function make(Core $core, TcpConnection $connection, string $data): Command
    {
        echo $data . PHP_EOL;
        $options = json_decode($data, true);
        $options = array_replace_recursive([
            'command' => 'say',
            'params'  => [
                'content' => 'hello world!',
            ],
        ], $options ?? []);
        var_dump($options);

        $class = self::$commands[$options['command']] ?? null;
        if (!$class || !class_exists($class)) {
            $class   = SayCommand::class;
            $options = [
                'params' => [
                    'content' => sprintf('Command %s is not supported.', $options['command']),
                ],
            ];
        }

        return new $class($core, $connection, $options);
    }

    /**
     * @return string
     */
    final public function execute(): string
    {
        try {
            if (!method_exists($this, 'run')) {
                throw new \Exception('No specific process with self::run');
            }

            $rm         = new \ReflectionMethod($this, 'run');
            $parameters = $rm->getParameters();
            $args       = [];
            foreach ($parameters as $parameter) {
                $pName = $parameter->getName();
                try {
                    $args[$parameter->getPosition()] = $this->config['params'][$pName] ?? $parameter->getDefaultValue();
                } catch (\ReflectionException $e) {
                    throw new \Exception('Missing parameter:' . $pName);
                }
            }
            ksort($args);
            $response = [
                'data' => $rm->invokeArgs($this, $args),
            ];
        } catch (\Exception $e) {
            $response = [
                'error' => 1,
                'data'  => (string)$e,
            ];
        }

        return json_encode(array_merge([
            'error' => 0,
            'data'  => null,
        ], $response));
    }
}
