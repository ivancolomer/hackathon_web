<?php

namespace App\Models;

use App\Config;

class Json extends \Core\Model {

    public static function send($arr) {
        View::renderTemplate('json.html', [
            "text" => json_encode($arr)
        ]);
    }
}