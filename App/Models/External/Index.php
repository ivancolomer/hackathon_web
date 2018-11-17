<?php

namespace App\Models\External;

use PDO;

class Index extends \Core\Model {

    public static function getServerStatus() {
        
        $db = static::getDB();

        $registered_accounts = 0;
        $registered_organizations = 0;

        if($row = $db->query("SELECT COUNT(*) FROM account;", PDO::FETCH_NUM)->fetch()) {
            $registered_accounts = (int)$row[0];
        }

        if($row = $db->query("SELECT COUNT(*) FROM organization;", PDO::FETCH_NUM)->fetch()) {
            $registered_organizations = (int)$row[0];
        }

        return [$registered_accounts, $registered_organizations];
    }
}