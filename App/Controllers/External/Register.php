<?php

namespace App\Controllers\External;

use \Core\View;

class Register extends \Core\Controller {

    protected function before() {

        if(isset($_SESSION['accountID'], $_SESSION['sessionID'])) {
            header('Location: /internal/home');
            return false;
        }  
    }

    public function Action() {

        if(!isset($_POST['register_username'], $_POST['register_password'], $_POST['register_re_password'], $_POST['register_email'])) {
            View::renderTemplate('External/register.html', [
                "siteKey" => \App\Config::CAPTCHA_SITE_KEY
            ]);
            return;
        }

        $username = preg_replace('/\s+/', '', $_POST['register_username']);
        $password = $_POST['register_password'];
        $re_password = $_POST['register_re_password'];
        $email = $_POST['register_email'];

        $errors = [];

        if (!isset($_POST['g-recaptcha-response'])) {
            $errors[] = "Error with recaptcha response.";
        } else {
            $recaptcha = new \ReCaptcha\ReCaptcha(\App\Config::CAPTCHA_SECRET);

            $resp = $recaptcha->setExpectedHostname($_SERVER['SERVER_NAME'])
                              ->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

            if (!$resp->isSuccess()) {
                $errors[] = "Error with verifying recaptcha response.";
            }
        }

        if(!isset($_POST['register_check_terms'])) {
            $errors[] = "You must accept our Terms and Conditons and the Privacy Policy.";
        }
        
        if(mb_strlen($username) !== mb_strlen($_POST['register_username'])) {
            $errors[] = "Your username mustn't contain whitespace characters."; 
        }   

        if(mb_strlen($username) < 5) {
            $errors[] = "Your username is too short. Please choose a new username which has between 5 and 20 characters."; 
        }
        else if(mb_strlen($username) > 20) {
            $errors[] = "Your username is too long. Please choose a new username which has between 5 and 20 characters.";
        }

        if(mb_strlen($password) < 8) {
            $errors[] = "Your password is too short. Please choose a new password which has between 8 and 45 characters."; 
        }
        else if(mb_strlen($password) > 45) {
            $errors[] = "Your password is too long. Please choose a new password which has between 8 and 45 characters.";
        }

        if($password !== $re_password) {
            $errors[] = "Your password and your confirm password don't match.";
        }

        if(mb_strlen($email) > 80 || preg_match("/^[a-z0-9_\.-]+@([a-z0-9]+([\-]+[a-z0-9]+)*\.)+[a-z]{2,7}$/i", $email) !== 1) {
            $errors[] = "Your e-mail address doesn't seem to be correct.";
        }

        if(count($errors) == 0) {
            $do_register = \App\Models\External\Register::doRegister($username, $password, $email);

            if($do_register[0]) {
                View::renderTemplate('External/register.html', [
                    "siteKey" => \App\Config::CAPTCHA_SITE_KEY,
                    "success" => true
                ]);

                fastcgi_finish_request();
                //SEND MAIL ASYNC
                \App\Models\External\Register::sendMail($email, $username, $do_register[1], $do_register[2]);
                return;
            }

            $errors[] = $do_register[1];
        }    
        
        View::renderTemplate('External/register.html', [
            "siteKey" => \App\Config::CAPTCHA_SITE_KEY,
            "errors" => $errors,
            "username" => $username,
            "email" => $email
        ]);
    } 
}