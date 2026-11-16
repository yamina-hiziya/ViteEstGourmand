<?php

namespace App\Tools;

class Csrf
{
    /*
        Renvoie le jeton de la session, en le créant s'il n'existe pas encore
    */
    public static function jeton(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /*
        Le champ caché à placer dans chaque formulaire en POST
    */
    public static function champ(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . self::jeton() . '">';
    }

    /*
        Vérifie le jeton reçu : lance une exception s'il est absent ou faux
    */
    public static function verifier(): void
    {
        $recu = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $recu)) {
            throw new \Exception('Votre session a expiré ou la demande est invalide. Merci de recharger la page et de réessayer.');
        }
    }
}