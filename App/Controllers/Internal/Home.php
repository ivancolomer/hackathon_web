<?php

namespace App\Controllers\Internal;

use \Core\View;

class Home extends \Core\Controller {

    private $user_info = null;
    private $alert_rows = null;

    protected function before() {
        
        if(!isset($_SESSION['accountID'], $_SESSION['sessionID'])) {
            header('Location: /');
            return false;
        }

        $this->user_info = \App\Models\Internal\User::getUserByIDAndSessionID($_SESSION['accountID'], $_SESSION['sessionID']);

        if($this->user_info === null) {
            unset($_SESSION['accountID'], $_SESSION['sessionID']);
            header('Location: /');
            return false;
        }

        $this->alert_rows = \App\Models\Internal\User::getAlertsByID($_SESSION['accountID']);
    }

    public function Action() {

        $alerts = [];
        foreach ($this->alert_rows as $row) {
            $alerts[] = ["time" => $row[0], "type" => $row[1] == "1" ? "warning" : "danger", "lat" => $row[3], "lon" => $row[2]];
        }

        View::renderTemplate('Internal/home.html', [
            'title' => 'Inicio',
            'page_id' => 0,
            'accountID' => $this->user_info[0],
            'alerts' => $alerts
        ]);
    }
}