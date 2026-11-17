<?php

namespace App\Controller;

use App\Controller\ContactController;
use App\Controller\CommandeController;
use App\Tools\Csrf;

class Controller
{
    public function route(): void
    {
        try {
            // Protection CSRF : toute requête POST doit contenir le bon jeton
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                Csrf::verifier();
            }
            if (isset($_GET['controller'])) {
                switch ($_GET['controller']) {
                    case 'page':
                        //charger controleur page
                        $controller = new PageController();
                        $controller->route();
                        break;
                    case 'auth':
                        //charger controleur auth
                        $controller = new AuthController();
                        $controller->route();
                        break;
                    case 'user':
                        $controller = new UserController();
                        $controller->route();
                        break;
                    case 'menu':
                        $controller = new MenuController();
                        $controller->route();
                        break;
                    case 'commande':
                        $controller = new CommandeController();
                        $controller->route();
                        break;
                    case 'contact':
                        $controller = new ContactController();
                        $controller->route();
                        break;
                    case 'compte':
                        $controller = new CompteController();
                        $controller->route();
                        break;
                    case 'employe':
                        $controller = new EmployeController();
                        $controller->route();
                        break;
                    case 'admin':
                        $controller = new AdminController();
                        $controller->route();
                        break;
                    default:
                        throw new \Exception("Le controleur n'existe pas");
                        break;
                }
            } else {
                //Chargement la page d'accueil si pas de controleur dans l'url
                $controller = new PageController();
                $controller->accueil();
            }
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function render(string $path, array $params = []): void
    {
        $filePath = _ROOTPATH_ . '/templates/' . $path . '.php';

        try {
            if (!file_exists($filePath)) {
                throw new \Exception("Fichier non trouvé : " . $filePath);
            } else {
                // Les horaires sont affichés dans le pied de page de toutes les pages
                if (!isset($params['horairesFooter'])) {
                    try {
                        $params['horairesFooter'] = (new \App\Repository\HoraireRepository())->findAll();
                    } catch (\Exception $e) {
                        $params['horairesFooter'] = [];
                    }
                }
                // Extrait chaque ligne du tableau et crée des variables pour chacune
                extract($params);
                require_once $filePath;
            }
        } catch (\Exception $e) {

            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
