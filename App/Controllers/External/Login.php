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

        $userid = 0;
        $password = "";
        $in_json = false;

        if(isset($_POST['login_userid'], $_POST['login_password'])) {
            $userid = intval($_POST['login_userid']);
            $password = $_POST['login_password'];  
        }
        else {
            $data = json_decode(file_get_contents('php://input'), true);
            if(isset($data['login_userid'], $data['login_password'])) {
                $in_json = true;
                $userid = intval($data['login_userid']);
                $password = $data['login_password'];  
            }
            else {
                View::renderTemplate('External/login.html', [
                    "page_id" => 1,
                    "title" => "Iniciar Sesión"
                ]);
                return;
            }
        }
        
        $errors = [];

        if(mb_strlen($password) < 8) {
            $errors[] = "Contraseña demasiado corta. Por favor, escoja una nueva contraseña entre 8 y 45 caracteres."; 
        }
        else if(mb_strlen($password) > 45) {
            $errors[] = "Contraseña demasiado larga. Por favor, escoja una nueva contraseña entre 8 y 45 caracteres.";
        }

        if(count($errors) == 0) {
            $result = \App\Models\External\Login::doLogin($userid, $password, $in_json);

            if($result[0]) {

                if($in_json) {
                    \App\Models\Json::send([
                        "sessionid" => $result[1]
                    ]);
                    return;
                }

                $_SESSION['accountID'] = $userid;
                $_SESSION['sessionID'] = $result[1];

                header('Location: /internal/home');
                return;
            }

            $errors[] = $result[1];
        }

        if($in_json) {
            \App\Models\Json::send([
                "errors" => $errors
            ]);
            return;
        }

        View::renderTemplate('External/login.html', [
                "page_id" => 1,
                "errors" => $errors,
                "title" => "Iniciar Sesión"
        ]);
    }  
}