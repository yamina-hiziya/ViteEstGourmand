<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CommandeRepository;

class CompteController extends Controller
{
    public function route(): void
    {
        try {
            // Tout l'espace client est réservé aux utilisateurs connectés
            if (!User::isLogged()) {
                header('Location: index.php?controller=auth&action=login');
                exit;
            }

            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'commandes':
                        $this->commandes();
                        break;
                    case 'detail':
                        $this->detail();
                        break;
                    case 'annuler':
                        $this->annuler();
                        break;
                    default:
                        throw new \Exception("Cette action n'existe pas : " . $_GET['action']);
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

    protected function commandes()
    {
        $commandeRepository = new CommandeRepository();
        $commandes = $commandeRepository->findAllByUtilisateurId(User::getCurrentUserId());

        $this->render('compte/commandes', [
            'commandes' => $commandes,
        ]);
    }

    protected function detail()
    {
        $numero = $_GET['numero'] ?? '';
        $commandeRepository = new CommandeRepository();
        $commande = $commandeRepository->findOneByNumeroForUser($numero, User::getCurrentUserId());

        if (!$commande) {
            throw new \Exception('Commande introuvable.');
        }

        $succes = $_SESSION['message_succes'] ?? null;
        unset($_SESSION['message_succes']);

        $this->render('compte/detail', [
            'commande' => $commande,
            'lignes' => $commandeRepository->findLignes($numero),
            'suivi' => $commandeRepository->findSuivi($numero),
            'succes' => $succes,
        ]);
    }

    protected function annuler()
    {
        if (!isset($_POST['annulerCommande'])) {
            throw new \Exception('Action non autorisée.');
        }

        $numero = $_POST['numero_commande'] ?? '';
        $commandeRepository = new CommandeRepository();
        $commandeRepository->annuler($numero, User::getCurrentUserId());

        $_SESSION['message_succes'] = 'Votre commande a bien été annulée.';
        header('Location: index.php?controller=compte&action=detail&numero=' . urlencode($numero));
        exit;
    }
}
