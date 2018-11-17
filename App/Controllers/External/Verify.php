<?php

namespace App\Controllers\External;

use \Core\View;

class Verify extends \Core\Controller {

    public function Action() {

        if(!isset($_GET['type'], $_GET['id'], $_GET['hash'])) {
            View::renderTemplate("404.html");
            return;
        }

        $type = $_GET['type'];
        $id = intval($_GET['id']);
        $hash = $_GET['hash'];

        $result = null;

        switch($type) {
            case "register":
                $result = \App\Models\External\Verify::doVerifyAccount(1, $id, $hash);
                if($result[0]) {
                    (new \App\Controllers\External\Index($this->route_params))->Action([], true);
                    return;
                }
                
                (new \App\Controllers\External\Index($this->route_params))->Action([$result[1]]);
                return;
                break;
            case "recovery":
                
                break;
        }

        View::renderTemplate("404.html");
    } 
}