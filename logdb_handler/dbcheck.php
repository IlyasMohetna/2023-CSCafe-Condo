<?php

namespace logdb_handler;

include_once "vendor/autoload.php";

use App\Utilitaire\Singleton_ConnexionPDO;

$connexionPDO = Singleton_ConnexionPDO::getInstance();

function checkLogsTableExists($pdo) {
    try {
        $query = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'cafe_log_db'");

        if ($query->rowCount() == 1) {
            return true;
        }
        return false;
    } catch (PDOException $e) {
        die("ERROR: Could not execute query: " . $e->getMessage());
    }
}

if (!checkLogsTableExists($connexionPDO)) {
    die("La base de donnée des logs est indisponible merci de l'installer pour continuer il suffit de lancer <b>php logsetup.php</b>");
}

?>
