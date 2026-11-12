<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\User;
use App\Repository\CommandeRepository;
use App\Repository\AvisRepository;
use App\Repository\HoraireRepository;

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
                    case 'avis':
                        $this->avis();
                        break;
                    case 'moderer':
                        $this->moderer();
                        break;
                    case 'horaires':
                        $this->horaires();
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

    protected function avis()
    {
        $avisRepository = new AvisRepository();

        $succes = $_SESSION['message_succes'] ?? null;
        unset($_SESSION['message_succes']);

        $this->render('employe/avis', [
            'avisListe' => $avisRepository->findEnAttente(),
            'succes' => $succes,
        ]);
    }

    protected function moderer()
    {
        $avisId = (int) ($_POST['avis_id'] ?? 0);
        $decision = $_POST['decision'] ?? '';

        if (!in_array($decision, ['validé', 'refusé'], true)) {
            throw new \Exception('Décision invalide.');
        }

        $avisRepository = new AvisRepository();
        if ($avisRepository->changerStatut($avisId, $decision)) {
            $_SESSION['message_succes'] = 'L\'avis a bien été ' . $decision . '.';
        }

        header('Location: index.php?controller=employe&action=avis');
        exit;
    }

    protected function horaires()
    {
        $horaireRepository = new HoraireRepository();
        $errors = [];

        if (isset($_POST['saveHoraires'])) {
            $aEnregistrer = [];

            foreach ($_POST['horaires'] ?? [] as $id => $donnees) {
                $id = (int) $id;

                // Jour fermé : les deux heures restent vides
                if (!empty($donnees['ferme'])) {
                    $aEnregistrer[$id] = ['ouverture' => null, 'fermeture' => null];
                    continue;
                }

                $ouverture = $donnees['ouverture'] ?? '';
                $fermeture = $donnees['fermeture'] ?? '';

                if (!preg_match('/^\d{2}:\d{2}$/', $ouverture) || !preg_match('/^\d{2}:\d{2}$/', $fermeture)) {
                    $errors[] = 'Indiquez une heure d\'ouverture et de fermeture pour chaque jour ouvert.';
                } elseif ($ouverture >= $fermeture) {
                    $errors[] = 'L\'heure d\'ouverture doit être avant l\'heure de fermeture.';
                } else {
                    $aEnregistrer[$id] = ['ouverture' => $ouverture, 'fermeture' => $fermeture];
                }
            }

            if (empty($errors)) {
                $horaireRepository->updateAll($aEnregistrer);
                $_SESSION['message_succes'] = 'Les horaires ont bien été mis à jour.';
                header('Location: index.php?controller=employe&action=horaires');
                exit;
            }
        }

        $succes = $_SESSION['message_succes'] ?? null;
        unset($_SESSION['message_succes']);

        $this->render('employe/horaires', [
            'horaires' => $horaireRepository->findAll(),
            'errors' => array_unique($errors),
            'succes' => $succes,
        ]);
    }
}
