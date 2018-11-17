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

    public static function sendMail($email, $username, $hashID, $hash) {
        try {
            $mailer = \App\Models\Mailer::getPHPMailer($email);

            $mailer->Subject = 'Signup | Verification of ' . Config::APP_NAME;

            $mailer->Body     = '<h1>Confirm your email address in ' . Config::APP_NAME . '</h1><br>';
            $mailer->Body    .= '<h3>Hello ' . $username . '! We just need to check that ' . $email . ' is your e-mail address. As soon as we have confirmed it, you will be able to login to ' . Config::APP_NAME . '.</h3><br>';
            $mailer->Body    .= '<a href="' . \Core\Utils::parseUrlFromString('verify?type=register&id=' . $hashID . '&hash=' . $hash) . '"><h2>Confirm e-mail address</h2></a><br>';
            $mailer->Body    .= '<h4>' . Config::APP_NAME . ' Team</h4>';
            
            $mailer->AltBody  = 'Confirm your email address in ' . Config::APP_NAME . '\r\n';
            $mailer->AltBody .= 'Hello ' . $username . '! We just need to check that ' . $email . ' is your e-mail address. As soon as we have confirmed it, you will be able to login to ' . Config::APP_NAME . '.\r\n';
            $mailer->AltBody .= 'Navigate to this url to confirm:\r\n';
            $mailer->AltBody .= \Core\Utils::parseUrlFromString('verify?type=register&id=' . $hashID . '&hash=' . $hash) . '\r\n';
            $mailer->AltBody .= '\r\n\r\n' . Config::APP_NAME . ' Team';

            $mailer->send();
        }
        catch (Exception $e) {
            \Core\Error::saveException($e);
        }
    }
}