<?php

namespace App\Models\Internal;

use PDO;

class User extends \Core\Model {

    public static function getUserByIDAndSessionID($accountID, $sessionID) {
        
        $db = static::getDB();

        $stmt = $db->prepare("SELECT a.account_id, t.mail, t.organization_id FROM account a INNER JOIN teacher_account t ON (a.account_id = ? AND a.session_id = ? AND a.account_id = t.account_id);");
        $stmt->bindValue(1, $accountID, PDO::PARAM_INT);
        $stmt->bindValue(2, $sessionID, PDO::PARAM_STR);

        $stmt->execute();
            
        if($row = $stmt->fetch(PDO::FETCH_NUM)) {
            return $row;
        }

        return null;
    }
}