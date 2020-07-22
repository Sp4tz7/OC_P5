<?php

namespace Application\Pdo;

use Config\Config;

class PDOFactory
{
    public static function getMysqlConnexion()
    {
        $data     = Config::getDbSettings();
        $dataBase = new \PDO(
            'mysql:host='.$data['host'].';dbname='.$data['dbname'].'',
            $data['username'],
            $data['password']
        );
        $dataBase->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $dataBase;
    }
}
