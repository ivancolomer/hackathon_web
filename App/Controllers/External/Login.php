<?php

namespace App\Controllers\External;

use \Core\View;

class Login extends \Core\Controller {

    protected function before() {

        if(isset($_SESSION['accountID'], $_SESSION['sessionID'])) {
            header('Location: /internal/home');
            return false;
        }  
    }

    public function Action() {

        if(!isset($_POST['login_userid'], $_POST['login_password'])) {
            (new \App\Controllers\External\Index($this->route_params))->Action();
            return;
        }

        $in_json = isset($_POST['json']) && intval($_POST['json']) === 1;

        $userid = intval($_POST['login_userid']);
        $password = $_POST['login_password'];
        
        $errors = [];

        if(mb_strlen($password) < 8) {
            $errors[] = "Your password is too short. Please choose a new password which has between 8 and 45 characters."; 
        }
        else if(mb_strlen($password) > 45) {
            $errors[] = "Your password is too long. Please choose a new password which has between 8 and 45 characters.";
        }

        if(count($errors) == 0) {
            $result = \App\Models\External\Login::doLogin($userid, $password);

            if($result[0]) {
                $_SESSION['accountID'] = $result[1];
                $_SESSION['sessionID'] = $result[2];

                if($result[3]) {
                    header('Location: /internal/home');
                    return;
                } 
				
                $result = \App\Models\Internal\User::doSuperRegister($_SESSION['accountID']);
				if($result) {
					header('Location: /internal/home');
					return;
				}
				
				unset($_SESSION['accountID'], $_SESSION['sessionID']);
				(new \App\Controllers\External\Index($this->route_params))->Action(["Unexpected error!"]);
                return;
            }

            $errors[] = $result[1];
        }

        (new \App\Controllers\External\Index($this->route_params))->Action($errors);
    }  
}