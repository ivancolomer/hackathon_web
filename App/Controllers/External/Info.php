<?php

namespace App\Controllers\External;

use \Core\View;

class Index extends \Core\Controller {

    public function Action() {

        View::renderTemplate('External/info.html', [
            
        ]);       
    }
}