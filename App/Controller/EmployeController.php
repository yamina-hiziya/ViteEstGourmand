<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\User;
use App\Repository\CommandeRepository;

class EmployeController extends Controller
{
    public function route(): void
    {
        try {
            // Réservé aux employés et à l'administrateur
            if (!User::isEmploye() && !User::isAdmin()) {
                throw new \Exception('Accès réservé au personnel.');
            }

            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'commandes':
                        $this->commandes();
                        break;
                        case 'commande':
                            $this->commande();
                            break;
                        case 'statut':
                            $this->statut();
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
        $statut = $_GET['statut'] ?? '';
        $client = trim($_GET['client'] ?? '');

        // On n'accepte que les statuts de la liste officielle
        if (!in_array($statut, Commande::STATUTS, true)) {
            $statut = '';
        }

        $commandeRepository = new CommandeRepository();
        $commandes = $commandeRepository->findAllForEmploye($statut, $client);

        $this->render('employe/commandes', [
            'commandes' => $commandes,
            'statut' => $statut,
            'client' => $client,
        ]);
    }

    protected function commande()
    {
        $numero = $_GET['numero'] ?? '';
        $commandeRepository = new CommandeRepository();
        $commande = $commandeRepository->findOneByNumeroForEmploye($numero);

        if (!$commande) {
            throw new \Exception('Commande introuvable.');
        }

        $succes = $_SESSION['message_succes'] ?? null;
        $erreur = $_SESSION['message_erreur'] ?? null;
        unset($_SESSION['message_succes'], $_SESSION['message_erreur']);

        $this->render('employe/commande', [
            'commande' => $commande,
            'lignes' => $commandeRepository->findLignes($numero),
            'suivi' => $commandeRepository->findSuivi($numero),
            'succes' => $succes,
            'erreur' => $erreur,
        ]);
    }

    protected function statut()
    {
        if (!isset($_POST['changerStatut'])) {
            throw new \Exception('Action non autorisée.');
        }

        $numero = $_POST['numero_commande'] ?? '';
        $statut = $_POST['statut'] ?? '';
        $motif = trim($_POST['motif'] ?? '');
        $modeContact = $_POST['mode_contact'] ?? '';

        $retour = 'Location: index.php?controller=employe&action=commande&numero=' . urlencode($numero);

        // Le statut doit faire partie de la liste officielle
        if (!in_array($statut, Commande::STATUTS, true)) {
            $_SESSION['message_erreur'] = 'Statut invalide.';
            header($retour);
            exit;
        }

        // Une annulation impose d'avoir contacté le client
        if ($statut === 'annulée' && ($motif === '' || !in_array($modeContact, ['téléphone', 'email'], true))) {
            $_SESSION['message_erreur'] = 'Pour annuler, indiquez le motif et le mode de contact utilisé avec le client.';
            header($retour);
            exit;
        }

        $commandeRepository = new CommandeRepository();
        $commandeRepository->changerStatut(
            $numero,
            $statut,
            $motif !== '' ? $motif : null,
            $modeContact !== '' ? $modeContact : null
        );

        $_SESSION['message_succes'] = 'Le statut de la commande est maintenant : ' . $statut . '.';
        header($retour);
        exit;
    }
}
