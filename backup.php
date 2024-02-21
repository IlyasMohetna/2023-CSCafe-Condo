<?php

include_once "vendor/autoload.php";

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;

$logger = new Logger('backup');
$logger->pushHandler(new StreamHandler(__DIR__.'/auto_backup.log', Logger::DEBUG));

$log_pattern = __DIR__ . '/logs';
$filename_pattern = 'app-';
$zipfilename_pattern = 'app-'. date('Y-m-d', strtotime('-1 day')) .'.zip';

function zipLogFile($log_pattern, $filename_pattern, $zipfilename_pattern) {
    global $logger;
    $logFiles = glob($log_pattern . '/' . $filename_pattern . '*.log');

    $zip = new ZipArchive();
    $zipPath = $log_pattern . '/' . $zipfilename_pattern;
    if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
        foreach ($logFiles as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();
        $logger->info('Le fichier log est zipé avec succès', ['fichier_log' => basename($file), 'zip_du_log' => $zipfilename_pattern]);
        foreach ($logFiles as $file) {
            unlink($file);
            $logger->debug('Ancien fichier de log supprimé', ['fichier_log_supprimé' => basename($file)]);
        }
    } else {
        $logger->error('Erreur lors de la création d\'un fichier zip pour le backup');
    }
}

zipLogFile($log_pattern, $filename_pattern, $zipfilename_pattern);