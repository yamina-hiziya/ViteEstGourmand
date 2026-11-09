<?php

namespace App\Controller;


class PageController extends Controller
{
    public function route(): void
    {
        try {
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'accueil':
                        //charger controleur page
                        $this->accueil();
                        break;
                    default:
                        throw new \Exception("Cette action n'existe pas : ".$_GET['action']);
                        break;
                }
            } else {
                throw new \Exception("Aucune action détectée");
            }
        } catch(\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }

    }

    /*
    Exemple d'appel depuis l'url
        ?controller=page&action=home
    */
    protected function accueil()
    {   
        $this->render('page/accueil', [
            'Vite&Gourmand' => "Vite&Gourmand",
            'Bienvenue' => "John",
        ]);

    }

}