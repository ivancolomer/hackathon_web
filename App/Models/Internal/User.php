<?php

namespace App\Models\Internal;

use PDO;

class User extends \Core\Model {

    public static function getUserByIDAndSessionID($accountID, $sessionID) {
        
        $db = static::getDB();

        $stmt = $db->prepare("SELECT a.account_id, a.name, a.mail, u.level, u.experience FROM account a INNER JOIN user_account u ON (a.account_id = ? AND a.session_id = ? AND a.account_id = u.account_id) LIMIT 1;");
        $stmt->bindValue(1, $accountID, PDO::PARAM_INT);
        $stmt->bindValue(2, $sessionID, PDO::PARAM_STR);

        $stmt->execute();
            
        if($row = $stmt->fetch(PDO::FETCH_NUM)) {
            return $row;
        }

        return null;
    }

    public static function doSuperRegister($accountID) {
        
        $db = static::getDB();

        try {  
            $db->beginTransaction();

            //player
            $sql = "INSERT INTO user (account_id, last_ip) VALUES (?, ?); ";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(1, $accountID, PDO::PARAM_INT);
            $stmt->bindValue(2, $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
            $stmt->execute();

            $db->commit();

            return true;

        } catch (\PDOException $e) {
            $db->rollBack();
            \Core\Error::saveException($e);
        }

        return false;
    }
}