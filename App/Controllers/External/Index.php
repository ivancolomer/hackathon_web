<?php

namespace App\Controllers\External;

use \Core\View;

class Index extends \Core\Controller {

    protected function before() {

        if(isset($_SESSION['accountID'], $_SESSION['sessionID'])) {
            header('Location: /internal/home');
            return false;
        }  
    }

    public function Action($errors = [], $account_confirmed = false) {
        
        $registered_count = \App\Models\External\Index::getServerStatus();
        View::renderTemplate('External/index.html', [
            "users_registered" => number_format($registered_count[0], 0, '.', ','),
            "organizations_registered" => number_format($registered_count[1], 0, '.', ','),
            "errors" => $errors
        ]);       
    }
}