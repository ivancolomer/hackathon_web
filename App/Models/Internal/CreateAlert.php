<?php

namespace App\Models\Internal;

use PDO;

class CreateAlert extends \Core\Model {

    public static function createAlert($accountID, $sessionID, $alert_type, $alert_lat, $alert_long) {
        
        $db = static::getDB();

        $stmt = $db->prepare("SELECT s.account_id FROM student_account s INNER JOIN account a ON(a.account_id = s.account_id AND a.account_id = ? AND a.session_id = ?);");
        $stmt->bindValue(1, $accountID, PDO::PARAM_INT);
        $stmt->bindValue(2, $sessionID, PDO::PARAM_STR);
        
        $stmt->execute();
        
        if($user_info = $stmt->fetch(PDO::FETCH_NUM)) {
            
            $stmt = $db->prepare("INSERT INTO alert (account_id, latitude, altitude, alert_type) VALUES (?, ?, ?, ?);");
            $stmt->bindValue(1, $accountID, PDO::PARAM_INT);
            $stmt->bindValue(2, $alert_lat, PDO::PARAM_STR);
            $stmt->bindValue(3, $alert_long, PDO::PARAM_STR);
            $stmt->bindValue(4, $alert_type, PDO::PARAM_INT);
            
            $stmt->execute();
        } 
    }
}