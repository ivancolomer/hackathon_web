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
                "title" => "Registro",
                "page_id" => 3,
            ]);
            return;
        }

        $username = preg_replace('/\s+/', '', $_POST['register_username']);
        $password = $_POST['register_password'];
        $re_password = $_POST['register_re_password'];
        $email = $_POST['register_email'];

        $errors = [];

        if(!isset($_POST['register_check_terms'])) {
            $errors[] = "Usted debe aceptar nuestros Términos y Condiciones de Uso.";
        }
        
        if(mb_strlen($username) !== mb_strlen($_POST['register_username'])) {
            $errors[] = "Su nombre de usuario no debe contener espacios en blanco."; 
        }   

        if(mb_strlen($username) < 5) {
            $errors[] = "Nombre de usuario demasiado corto. Por favor, escoja un nuevo nombre de usuario entre 5 y 20 caracteres."; 
        }
        else if(mb_strlen($username) > 20) {
            $errors[] = "Nombre de usuario demasiado largo. Por favor, escoja un nuevo nombre de usuario entre 5 y 20 caracteres.";
        }

        if(mb_strlen($password) < 8) {
            $errors[] = "Contraseña demasiado corta. Por favor, escoja una nueva contraseña entre 8 y 45 caracteres."; 
        }
        else if(mb_strlen($password) > 45) {
            $errors[] = "Contraseña demasiado larga. Por favor, escoja una nueva contraseña entre 8 y 45 caracteres.";
        }

        if($password !== $re_password) {
            $errors[] = "Sus contraseñas no coinciden.";
        }

        if(mb_strlen($email) > 80 || preg_match("/^[a-z0-9_\.-]+@([a-z0-9]+([\-]+[a-z0-9]+)*\.)+[a-z]{2,7}$/i", $email) !== 1) {
            $errors[] = "Su dirección de correo no parece ser cierta.";
        }

        if(count($errors) == 0) {
            $do_register = \App\Models\External\Register::doRegister($username, $password, $email);

            if($do_register[0]) {
                View::renderTemplate('External/register.html', [
                    "success" => true,
                    "page_id" => 3,
                    "title" => "Registro"
                ]);
                return;
            }

            $errors[] = $do_register[1];
        }    
        
        View::renderTemplate('External/register.html', [
            "errors" => $errors,
            "username" => $username,
            "email" => $email,
            "page_id" => 3,
            "title" => "Registro"
        ]);
    } 
}