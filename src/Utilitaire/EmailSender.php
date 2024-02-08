<?php

namespace App\Utilitaire;

use PHPMailer\PHPMailer\PHPMailer;

class EmailSender
{
    private static ?PHPMailer $mailer = null;

    private function __construct()
    {
        self::$mailer = new PHPMailer;
        self::$mailer->isSMTP();
        self::$mailer->Host = '127.0.0.1';
        self::$mailer->Port = 1025; // Port non crypté
        self::$mailer->SMTPAuth = false; // Pas d’authentification
        self::$mailer->SMTPAutoTLS = false; // Pas de certificat TLS
        self::$mailer->CharSet = 'UTF-8'; // Set charset to UTF-8
        self::$mailer->Encoding = 'base64';
        self::$mailer->setFrom('test@labruleriecomtoise.fr', 'admin');
    }

    public static function getInstance(): PHPMailer
    {
        if (is_null(self::$mailer)) {
            new EmailSender();
        }
        return self::$mailer;
    }

    public static function sendEmail(string $recipientEmail, string $subject, string $body): bool
    {
        $mailer = self::getInstance();
        $mailer->addAddress($recipientEmail);
        $mailer->Subject = $subject;
        $mailer->isHTML(false);
        $mailer->Body = $body;
        
        

        if (!$mailer->send()) {
            return false;
        }

        return true;
    }
}
