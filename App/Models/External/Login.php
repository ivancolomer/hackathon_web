<?php

namespace App\Models\External;

use PDO;

class Login extends \Core\Model {

    public static function doLogin($userid, $password, $in_json) {
        
        $db = static::getDB();

        try {  
            $db->beginTransaction();

            $stmt = $db->prepare("SELECT a.password_hash, s.gender, t.mail FROM account a LEFT JOIN student_account s ON(a.account_id = s.account_id AND a.account_id = ?) LEFT JOIN teacher_account t ON(a.account_id = t.account_id AND a.account_id = ?);");
            $stmt->bindValue(1, $userid, PDO::PARAM_INT);
            $stmt->bindValue(2, $userid, PDO::PARAM_INT);
            
            $stmt->execute();
            
            if($user_info = $stmt->fetch(PDO::FETCH_NUM)) {
                $hash = $user_info[0];
                $is_student = ($user_info[1] != "");
                $is_teacher = ($user_info[2] != "");

                if(password_verify($password, $hash)) {
                    if (password_needs_rehash($hash, PASSWORD_DEFAULT, \Core\Utils::getHashOptions())) {
                        // Create a new hash and update it into database
                        $newHash = password_hash($password, PASSWORD_DEFAULT, \Core\Utils::getHashOptions());

                        $stmt = $db->prepare("UPDATE account SET password_hash = ? WHERE account_id = ?;");
                        $stmt->bindValue(1, $newHash, PDO::PARAM_STR);
                        $stmt->bindValue(2, $userid, PDO::PARAM_INT);
                        
                        $stmt->execute();
                    }

                    if($in_json && $is_teacher || !$in_json && $is_student) {
                        $db->commit();
                        return  [false, "No se encuentra una cuenta con este id de usuario y/o constraseña."];  
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
                    return  [false, "No se encuentra una cuenta con este id de usuario y/o constraseña."];  
                }  
            } else {
                $db->rollBack();
                return  [false, "No se encuentra una cuenta con este id de usuario y/o constraseña."];
            } 
        } catch (\PDOException $e) {
            $db->rollBack();
            //return [false, $e->getMessage()];
        }

        return [false, "Error desconocido."];
    }
}