<?php

namespace Application\PDO;

use Config\Config;

class PDOFactory
{
    public static function getMysqlConnexion()
    {
        $data = Config::getDbSettings();
        $db   = new \PDO(
            'mysql:host='.$data['host'].';dbname='.$data['dbname'].'',
            $data['username'],
            $data['password']
        );
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $db;
    }
}
