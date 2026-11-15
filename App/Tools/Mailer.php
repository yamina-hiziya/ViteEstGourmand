<?php

namespace App\Tools;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    /*
        Envoie un mail au format HTML.
        Renvoie true si l'envoi a réussi, false sinon (sans bloquer le site).
    */
    public static function envoyer(string $destinataire, string $sujet, string $contenuHtml): bool
    {
        $config = require _ROOTPATH_ . '/App/Config/mail_config.php';
        $mail = new PHPMailer(true);

        try {
            // Connexion au serveur d'envoi (SMTP)
            $mail->isSMTP();
            $mail->Host = $config['host'];
            $mail->Port = $config['port'];
            $mail->CharSet = 'UTF-8';

            if ($config['username'] !== '') {
                $mail->SMTPAuth = true;
                $mail->Username = $config['username'];
                $mail->Password = $config['password'];
            }
            if ($config['secure'] !== '') {
                $mail->SMTPSecure = $config['secure'];
            } else {
                $mail->SMTPAutoTLS = false;
            }

            // Expéditeur, destinataire et contenu
            $mail->setFrom($config['from_email'], $config['from_name']);
            $mail->addAddress($destinataire);
            $mail->isHTML(true);
            $mail->Subject = $sujet;
            $mail->Body = self::gabarit($sujet, $contenuHtml);
            $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], "\n", $contenuHtml));

            $mail->send();
            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    /*
        Mise en page commune à tous les mails, aux couleurs de la charte
    */
    public static function gabarit(string $titre, string $contenu): string
    {
        return '
        <div style="font-family: Arial, sans-serif; background: #F7F4EE; padding: 24px;">
            <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden;">
                <div style="background: #1B4A8A; color: #ffffff; padding: 20px; text-align: center;">
                    <h1 style="margin: 0; font-size: 22px;">Vite &amp; Gourmand</h1>
                </div>
                <div style="padding: 24px; color: #333333; line-height: 1.6;">
                    <h2 style="color: #C9A84C; font-size: 18px; margin-top: 0;">' . htmlspecialchars($titre) . '</h2>
                    ' . $contenu . '
                </div>
                <div style="background: #F7F4EE; padding: 12px; text-align: center; font-size: 12px; color: #777777;">
                    Vite &amp; Gourmand — 12 rue des Gourmets, 33000 Bordeaux
                </div>
            </div>
        </div>';
    }
}