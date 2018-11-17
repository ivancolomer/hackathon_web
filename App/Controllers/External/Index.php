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
        
        $server_status = \App\Models\External\Index::getServerStatus();
        View::renderTemplate('External/index.html', [
            "players_registered" => number_format($server_status, 0, '.', ','),
            "errors" => $errors,
            "confirmed" => $account_confirmed
        ]);       
    }
}