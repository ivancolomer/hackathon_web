<?php

namespace App\Controllers\External;

use \Core\View;

class Info extends \Core\Controller {

    public function Action() {

        View::renderTemplate('External/info.html', [
            "page_id" => 2,
            "title" => "Información"
        ]);       
    }
}