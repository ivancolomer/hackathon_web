<?php

namespace App\Controllers\Internal;

use \Core\View;

class Home extends \Core\Controller {

    private $user_info = null;

    protected function before() {
        
        if(!isset($_SESSION['accountID'], $_SESSION['sessionID'])) {
            echo $_SESSION['accountID'];
            $_SESSION['sessionID'];
            header('Location: /a2');
            return false;
        }

        $this->user_info = \App\Models\Internal\User::getUserByIDAndSessionID($_SESSION['accountID'], $_SESSION['sessionID']);

        if($this->user_info === null) {
            unset($_SESSION['accountID'], $_SESSION['sessionID']);
            header('Location: /a3');
            return false;
        }
    }

    public function Action() {

        View::renderTemplate('Internal/home.html', [
            'title' => 'Inicio',
            'page_id' => 0,
            'accountID' => $user_info[0]
        ]);
    }
}