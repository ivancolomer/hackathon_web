<?php

namespace App\Controllers\Internal;

use \Core\View;

class Create extends \Core\Controller {

    public function Action() {

        $data = json_decode(file_get_contents('php://input'), true);
        if(isset($data['user_id'], $data['session_id'], $data['alert_type'], $data['alert_lat'], $data['alert_lon'])) {
            \App\Models\Internal\CreateAlert::createAlert($data['user_id'], $data['session_id'], $data['alert_type'], $data['alert_lat'], $data['alert_lon']);  
        }
        header('Location: /');
    }  
}