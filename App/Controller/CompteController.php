<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CommandeRepository;
use App\Repository\AvisRepository;

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
                    case 'avis':
                        $this->avis();
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
        $erreur = $_SESSION['message_erreur'] ?? null;
        unset($_SESSION['message_erreur']);

        $this->render('compte/detail', [
            'commande' => $commande,
            'lignes' => $commandeRepository->findLignes($numero),
            'suivi' => $commandeRepository->findSuivi($numero),
            'succes' => $succes,
            'avis' => (new AvisRepository())->findOneByNumeroCommande($numero),
            'erreur' => $erreur,
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

    protected function avis()
    {
        if (!isset($_POST['envoyerAvis'])) {
            throw new \Exception('Action non autorisée.');
        }

        $numero = $_POST['numero_commande'] ?? '';
        $note = (int) ($_POST['note'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        // La commande doit appartenir à l'utilisateur et être terminée
        $commandeRepository = new CommandeRepository();
        $commande = $commandeRepository->findOneByNumeroForUser($numero, User::getCurrentUserId());
        if (!$commande || $commande['statut'] !== 'terminée') {
            throw new \Exception('Vous ne pouvez pas laisser d\'avis sur cette commande.');
        }

        // Un seul avis par commande
        $avisRepository = new AvisRepository();
        if ($avisRepository->findOneByNumeroCommande($numero)) {
            throw new \Exception('Vous avez déjà laissé un avis sur cette commande.');
        }

        // Note entre 1 et 5, et commentaire rempli
        if ($note < 1 || $note > 5 || $description === '') {
            $_SESSION['message_erreur'] = 'Merci de choisir une note entre 1 et 5 et d\'écrire un commentaire.';
        } else {
            $avisRepository->creer($note, $description, $numero, User::getCurrentUserId());
            $_SESSION['message_succes'] = 'Merci pour votre avis ! Il sera publié après validation par notre équipe.';
        }

        header('Location: index.php?controller=compte&action=detail&numero=' . urlencode($numero));
        exit;
    }
}
