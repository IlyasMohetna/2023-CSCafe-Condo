<?php

include_once "vendor/autoload.php";
use App\Utilitaire\Singleton_ConnexionPDO;

$dbName = "CAFE_LOG_DB";

function createDatabase($pdo) {
    global $dbName;
    try {
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName`");
    } catch (PDOException $e) {
        die("Impossible de créer la base de données pour les LOG" . $e->getMessage());
    }
}

function createLogsTable($pdo) {
    global $dbName;
    try {
        $pdo->exec("USE `$dbName`");
        $pdo->exec("CREATE TABLE IF NOT EXISTS logs (
            log_id INT AUTO_INCREMENT PRIMARY KEY,
            log_causer_id INT,
            log_causer_type VARCHAR(255),
            log_target VARCHAR(255),
            log_comment VARCHAR(255),
            log_details TEXT NULL,
            log_date DATETIME
        )");
    } catch (PDOException $e) {
        die("Impossible de créer la table logs: " . $e->getMessage());
    }
}

function createDatabaseUsers($pdo) {
    global $dbName;
    $users = [
        'cafelog_admin' => 'ALL PRIVILEGES',
        'cafelog_service' => 'INSERT, SELECT, UPDATE',
        'cafelog_auditor' => 'SELECT',
    ];

    foreach ($users as $user => $privileges) {

        $pdo->exec("CREATE USER IF NOT EXISTS '$user'@'localhost' IDENTIFIED BY 'secret'");
        $pdo->exec("GRANT $privileges ON `$dbName`.* TO '$user'@'localhost'");
        $pdo->exec("FLUSH PRIVILEGES");

        echo "Créatiton de l'utilisateur :  ".$user." avec les prévilèges : ".$privileges."\n";
    }
}

$connexionPDO = Singleton_ConnexionPDO::getInstance();

createDatabase($connexionPDO);
createLogsTable($connexionPDO);
createDatabaseUsers($connexionPDO);

echo "L'installation est complète";