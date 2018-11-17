<?php

namespace App\Models\External;

use PDO;

class Index extends \Core\Model {

    public static function getServerStatus() {
        
        $db = static::getDB();

        if($row = $db->query("SELECT COUNT(*) FROM user_account;", PDO::FETCH_NUM)->fetch()) {
            return (int)$row[0];;
        }

        return 0;
    }
}