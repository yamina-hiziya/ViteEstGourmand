<?php

namespace App\Controller;
use App\Repository\AvisRepository;


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
                        throw new \Exception("Cette action n'existe pas : " . $_GET['action']);
                        break;
                }
            } else {
                throw new \Exception("Aucune action détectée");
            }
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /*
    Page d'accueil, avec les derniers avis validés
    Exemple d'appel depuis l'url : ?controller=page&action=accueil
*/
    protected function accueil()
    {
        $avisRepository = new AvisRepository();
        $avisValides = $avisRepository->findValides(6);

        $this->render('page/accueil', [
            'avisValides' => $avisValides,
        ]);
    }
}
