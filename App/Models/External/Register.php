<?php

namespace App\Models\External;

use PDO;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Config;

class Register extends \Core\Model {

    public static function doRegister($username, $password, $email) {
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT, \Core\Utils::getHashOptions());
        $hash = \Core\Utils::generateRandom(20, true, true);

        $db = static::getDB();

        try {  
            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO account (session_id, name, password_hash, mail, registered_ip) VALUES ('', ?, ?, ?, ?);");
            $stmt->bindValue(1, $username, PDO::PARAM_STR);
            $stmt->bindValue(2, $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(3, $email, PDO::PARAM_STR);
            $stmt->bindValue(4, $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
            
            $stmt->execute();
            $accountID = $db->lastInsertId();

            $stmt = $db->prepare("INSERT INTO confirm_hashes (account_id, hash_type, hash_code) VALUES (?, ?, ?);");
            $stmt->bindValue(1, $accountID, PDO::PARAM_INT);
            $stmt->bindValue(2, 1, PDO::PARAM_INT);
            $stmt->bindValue(3, $hash, PDO::PARAM_STR);

            $stmt->execute();
            $hashID = $db->lastInsertId();

            $db->commit();

            return [true, $hashID, $hash];

        } catch (\PDOException $e) {
            $db->rollBack();

            $error_message = $e->getMessage();

            if (strpos($error_message, 'account_name_key') !== false) {
                return [false, "There's already a player with that username."];
            }
            else if (strpos($error_message, 'account_mail_key') !== false) {
                return [false, "There's already a player with that e-mail."];
            }
        }

        return [false, "Unkown error."];
    }
}