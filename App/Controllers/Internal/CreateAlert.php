<?php

namespace App\Controllers\External;

use \Core\View;

class CreateAlert extends \Core\Controller {

    public function Action() {

        $data = json_decode(file_get_contents('php://input'), true);
        if(isset($data['user_id'], $data['session_id'], $data['alert_type'], $data['alert_lat'], $data['alert_lon'])) {
            \App\Models\Internal\CreateAlert::createAlert(intval($data['user_id']), $data['session_id'], intval($data['alert_type']), floatval($data['alert_lat']), floatval($data['alert_lon']));  
        }
    }  
}