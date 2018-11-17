<?php

namespace App\Models\External;

use PDO;

class Verify extends \Core\Model {

    public static function doVerifyAccount($type, $id, $hash) {
        
        $db = static::getDB();

        try {  
            $db->beginTransaction();

            $stmt = $db->prepare("DELETE FROM confirm_hashes WHERE hash_id = ? AND hash_type = ? AND hash_code = ? RETURNING account_id;");
            $stmt->bindValue(1, $id, PDO::PARAM_INT);
            $stmt->bindValue(2, $type, PDO::PARAM_INT);
            $stmt->bindValue(3, $hash, PDO::PARAM_STR);
            
            $stmt->execute();

            if($deleted_row = $stmt->fetch(PDO::FETCH_NUM)) {

                $stmt = $db->prepare("UPDATE account SET mail_verified = ? WHERE account_id = ?;");
                $stmt->bindValue(1, "1", PDO::PARAM_STR);
                $stmt->bindValue(2, $deleted_row[0], PDO::PARAM_INT);

                $stmt->execute();

                $db->commit();

                return [true];
            }

            $db->rollBack();
            return [false, "Wrong hash."];

        } catch (\PDOException $e) {
            $db->rollBack();
        }

        return [false, "Unkown error."];
    }
}