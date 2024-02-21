<?php

namespace LogHandler;

use PDO;

class Singleton_LOG_ConnexionPDO extends PDO
{
    protected static ?PDO $_PDO = null;

    private function __construct()
    {
        parent::__construct('mysql:host=127.0.0.1;dbname=cafe_log_db;charset=UTF8',
            "cafelog_service",
            "secret",
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            )
        );

    }

    public static function getInstance(): PDO
    {

        if (is_null(self::$_PDO)) {
            self::$_PDO = new Singleton_LOG_ConnexionPDO();
        }
        return self::$_PDO;
    }
}
