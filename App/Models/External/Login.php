<?php

namespace App\Models\External;

use PDO;

class Login extends \Core\Model {

    public static function doLogin($username, $password) {
        
        $db = static::getDB();

        try {  
            $db->beginTransaction();

            $stmt = $db->prepare("SELECT a.account_id, a.password_hash, a.mail_verified, u.level FROM account a LEFT JOIN user_account u ON (a.account_id = u.account_id) WHERE a.name = ? LIMIT 1;");
            $stmt->bindValue(1, $username, PDO::PARAM_STR);
            
            $stmt->execute();
            
            if($user_info = $stmt->fetch(PDO::FETCH_NUM)) {
                $accountID = (int) $user_info[0];
                $hash = $user_info[1];
                $is_mail_verified = ($user_info[2] == "1");
                $has_user = ($user_info[3] != "");

                if(password_verify($password, $hash)) {
                    if($is_mail_verified) { 
                        if (password_needs_rehash($hash, PASSWORD_DEFAULT, \Core\Utils::getHashOptions())) {
                            // Create a new hash and update it into database
                            $newHash = password_hash($password, PASSWORD_DEFAULT, \Core\Utils::getHashOptions());

                            $stmt = $db->prepare("UPDATE account SET password_hash = ? WHERE account_id = ?;");
                            $stmt->bindValue(1, $newHash, PDO::PARAM_STR);
                            $stmt->bindValue(2, $accountID, PDO::PARAM_INT);
                            
                            $stmt->execute();
                        }

                        $sessionID = \Core\Utils::generateRandom(20, true, false);

                        $stmt = $db->prepare("UPDATE account SET session_id = ?, last_time_logged = now() WHERE account_id = ?;");
                        $stmt->bindValue(1, $sessionID, PDO::PARAM_STR);
                        $stmt->bindValue(2, $accountID, PDO::PARAM_INT);
                        
                        $stmt->execute();
    
                        $db->commit();

                        return [true, $accountID, $sessionID, $has_user];

                    } else {
                        $db->rollBack();
                        return [false, "You must confirm your email address before proceeding."]; 
                    }    
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