<?php

namespace App\Controllers\External;

use \Core\View;

class Logout extends \Core\Controller {

    protected function before() {
        
        if(!isset($_SESSION['accountID'], $_SESSION['sessionID'])) {
            header('Location: /');
            return false;
        }
    }

    public function Action() {

        unset($_SESSION['accountID'], $_SESSION['sessionID']);
        header('Location: /');
    }
}