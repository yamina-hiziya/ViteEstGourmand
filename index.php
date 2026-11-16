<?php
// Affichage des erreurs uniquement en développement (localhost)
if (str_starts_with($_SERVER['HTTP_HOST'] ?? '', 'localhost')) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    // En production : les erreurs sont enregistrées dans un journal, jamais affichées
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/App/Config/config.php';

//Sécurise le cookie de session avec httponly
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => $_SERVER['SERVER_NAME'],
    'httponly' => true
]);

session_start();
define('_ROOTPATH_',__DIR__);
//define le chemin vers le dossier templates
define('_TEMPLATES_PATH', __DIR__ . '/templates');
//autoload des classes du projet
spl_autoload_register(function (string $class) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($file)){
        require_once $file;
    }
});

use App\Controller\Controller;

$controller = new Controller();
$controller->route();


