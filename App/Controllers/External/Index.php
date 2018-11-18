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

    public function Action() {
        
        //$registered_count = \App\Models\External\Index::getServerStatus();
        View::renderTemplate('External/index.html', [
            "page_id" => 0
            /*"users_registered" => number_format($registered_count[0], 0, '.', ','),
            "organizations_registered" => number_format($registered_count[1], 0, '.', ',')*/
        ]);       
    }
}