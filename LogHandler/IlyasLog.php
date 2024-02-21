<?php

namespace LogHandler;

use App\Utilitaire\Singleton_ConnexionPDO;
use LogHandler\Singleton_LOG_ConnexionPDO;

class IlyasLog {
    public function __construct() {
        $this->checkDatabaseExists();        
    }

    private function checkDatabaseExists() {
        $connexionPDO = Singleton_ConnexionPDO::getInstance();

        try {
            $query = $connexionPDO->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'cafe_log_db'");
            if ($query->rowCount() == 0) {
                die("La base de donnée des logs est indisponible. Merci de l'installer pour continuer. Il suffit de lancer <b>php logsetup.php</b>.");
            }
        } catch (\PDOException $e) {
            die("ERROR: Could not execute query: " . $e->getMessage());
        }
    }

    public function log($causer_id, $causer_type, $target, $comment, $details = null) {
        $pdo = Singleton_LOG_ConnexionPDO::getInstance();
        $stmt = $pdo->prepare("INSERT INTO logs (log_causer_id, log_causer_type, log_target, log_comment, log_details, log_date) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $causer_id,
            $causer_type,
            $target,
            $comment,
            $details ?? NULL
        ]);
    }
}
