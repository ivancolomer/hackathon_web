<?php

namespace Core;

class Utils {

    public static function generateRandom($length, $numbers = true, $letters = true, $otherChars = '') {
        
        $chars = '';
        $chars .= ($numbers) ? '0123456789' : '';
        $chars .= ($letters) ? 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' : '';
        $chars .= $otherChars;

        $str = '';
        $c = 0;
        while ($c < $length)
        { 
            $str .= substr($chars, rand(0, strlen($chars) -1), 1);
            $c++;
        }    
        return $str;
    }
    
    /*
     * Use it in queries where LIKE is needed.
     * 
     * Use-case:
     * $stmt = $db->prepare("... WHERE name LIKE ? ESCAPE '=' AND ...");
     * $stmt->bindValue(1, '%'.like($name, '=').'%', PDO::PARAM_STR);
     */
    public static function like($s, $e) {
        
        return str_replace(array($e, '_', '%'), array($e.$e, $e.'_', $e.'%'), $s);
    }

    public static function getHashOptions() {

        return ['cost' => 11];
    }

    public static function getCalculateHashCost() {
        $timeTarget = 0.25; // 250 milliseconds 

        $cost = 8;
        do {
            $cost++;
            $start = microtime(true);
            password_hash("test", PASSWORD_BCRYPT, ["cost" => $cost]);
            $end = microtime(true);
            echo "Cost " . $cost . " takes : " . ($end - $start) . "s<br/>";
        } while (($end - $start) < $timeTarget);

        echo "Appropriate Cost Found: " . $cost;
    }

    public static function parseUrlFromString($string) {
        return (\App\Config::USE_HTTPS ? "https" : "http") . "://" . \App\Config::DNS_NAME . "/" . $string;
    }
}