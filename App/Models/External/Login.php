<?php

namespace App\Models\External;

use PDO;

class Login extends \Core\Model {

    public static function doLogin($userid, $password) {
        
        $db = static::getDB();

        try {  
            $db->beginTransaction();

            $stmt = $db->prepare("SELECT password_hash FROM account WHERE account_id = ?;");
            $stmt->bindValue(1, $userid, PDO::PARAM_INT);
            
            $stmt->execute();
            
            if($user_info = $stmt->fetch(PDO::FETCH_NUM)) {
                $hash = $user_info[0];

                if(password_verify($password, $hash)) {
                    if (password_needs_rehash($hash, PASSWORD_DEFAULT, \Core\Utils::getHashOptions())) {
                        // Create a new hash and update it into database
                        $newHash = password_hash($password, PASSWORD_DEFAULT, \Core\Utils::getHashOptions());

                        $stmt = $db->prepare("UPDATE account SET password_hash = ? WHERE account_id = ?;");
                        $stmt->bindValue(1, $newHash, PDO::PARAM_STR);
                        $stmt->bindValue(2, $userid, PDO::PARAM_INT);
                        
                        $stmt->execute();
                    }

                    $sessionID = \Core\Utils::generateRandom(20, true, true);

                    $stmt = $db->prepare("UPDATE account SET session_id = ? WHERE account_id = ?;");
                    $stmt->bindValue(1, $sessionID, PDO::PARAM_STR);
                    $stmt->bindValue(2, $userid, PDO::PARAM_INT);
                    
                    $stmt->execute();

                    $db->commit();

                    return [true, $sessionID];  
                } else {
                    $db->rollBack(); 
                    return  [false, "No account with this username/password combination was found."];  
                }  
            } else {
                $db->rollBack();
                return  [false, "No account with this username/password combination was found."];
            } 
        } catch (\PDOException $e) {
            $db->rollBack();
        }

        return [false, "Unkown error."];
    }
}