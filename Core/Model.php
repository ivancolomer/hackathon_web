<?php

namespace Core;

use PDO;
use App\Config;

abstract class Model {

    protected static function getDB() {
        
        static $db = null;

        if ($db === null) {
            $dsn = 'pgsql:host=' . Config::DB_HOST . ';port=5432;dbname=' . Config::DB_NAME . ';options=\'--client_encoding=UTF8\'';
            $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $db;
    }
}