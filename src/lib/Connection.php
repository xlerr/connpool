<?php

namespace connpool\lib;

use Doctrine\DBAL\Driver\PDOException;

class Connection
{
    public $name;
    public $db;

    public function __construct(string $name, array $config)
    {
        $this->name = $name;

        $config = array_replace_recursive([
            'dsn'      => null,
            'username' => null,
            'password' => null,
            'option'   => [],
        ], $config);

        try {
            $this->db = new PDO($config['dsn'], $config['username'], $config['password'], $config['option']);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function build(string $sql, array $params = [])
    {
        $sth = $this->db->prepare($sql);

        $sth->execute($params);

        return $sth;
    }
}