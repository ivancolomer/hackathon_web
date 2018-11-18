<?php

namespace App\Models\External;

use PDO;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Config;

class Register extends \Core\Model {

    public static function doRegister($username, $password, $email, $center) {
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT, \Core\Utils::getHashOptions());
        $sessionID = \Core\Utils::generateRandom(20, true, true);

        $db = static::getDB();

        try {  
            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO account (session_id, name, password_hash) VALUES (?, ?, ?);");
            $stmt->bindValue(1, $sessionID, PDO::PARAM_STR);
            $stmt->bindValue(2, $username, PDO::PARAM_STR);
            $stmt->bindValue(3, $password_hash, PDO::PARAM_STR);
            
            $stmt->execute();
            $accountID = $db->lastInsertId();

            $stmt = $db->prepare("INSERT INTO organization (name) VALUES (?);");
            $stmt->bindValue(1, $center, PDO::PARAM_STR);

            $stmt->execute();
            $centerID = $db->lastInsertId();

            $stmt = $db->prepare("INSERT INTO teacher_account (account_id, mail, organization_id) VALUES (?, ?, ?);");
            $stmt->bindValue(1, $accountID, PDO::PARAM_INT);
            $stmt->bindValue(2, $email, PDO::PARAM_STR);
            $stmt->bindValue(3, $centerID, PDO::PARAM_INT);
            
            $stmt->execute();

            $stmt = $db->prepare("INSERT INTO courses (name, organization) VALUES (?, ?);");
            $stmt->bindValue(1, "1C-ESO", PDO::PARAM_STR);
            $stmt->bindValue(2, $centerID, PDO::PARAM_INT);
            
            $stmt->execute();
            $courseID = $db->lastInsertId();

            $stmt = $db->prepare("INSERT INTO teacher_on_course (account_id, course_id, category) VALUES (?, ?, ?);");
            $stmt->bindValue(1, $accountID, PDO::PARAM_INT);
            $stmt->bindValue(2, $courseID, PDO::PARAM_INT);
            $stmt->bindValue(3, 0, PDO::PARAM_INT);
            
            $stmt->execute();

            $db->commit();

            return [true, $accountID];

        } catch (\PDOException $e) {
            $db->rollBack();
            
        }

        return [false, "Unkown error."];
    }
}