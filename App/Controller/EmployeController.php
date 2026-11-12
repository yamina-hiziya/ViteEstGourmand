<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\User;
use App\Entity\Plat;
use App\Repository\CommandeRepository;
use App\Repository\AvisRepository;
use App\Repository\HoraireRepository;
use App\Repository\PlatRepository;
use App\Repository\AllergeneRepository;

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
                    case 'plats':
                        $this->plats();
                        break;
                    case 'plat':
                        $this->plat();
                        break;
                    case 'supprimerPlat':
                        $this->supprimerPlat();
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

    protected function plats()
    {
        $succes = $_SESSION['message_succes'] ?? null;
        $erreur = $_SESSION['message_erreur'] ?? null;
        unset($_SESSION['message_succes'], $_SESSION['message_erreur']);

        $this->render('employe/plats', [
            'plats' => (new PlatRepository())->findAllAvecAllergenes(),
            'succes' => $succes,
            'erreur' => $erreur,
        ]);
    }

    protected function plat()
    {
        $platRepository = new PlatRepository();
        $allergenes = (new AllergeneRepository())->findAll();
        $id = (int) ($_GET['id'] ?? 0);

        // Modification d'un plat existant, ou création d'un nouveau plat
        if ($id > 0) {
            $plat = $platRepository->findOneById($id);
            if (!$plat) {
                throw new \Exception('Plat introuvable.');
            }
            $allergenesCoches = $platRepository->findAllergeneIds($id);
        } else {
            $plat = new Plat();
            $allergenesCoches = [];
        }

        $errors = [];

        if (isset($_POST['savePlat'])) {
            $titre = trim($_POST['titre_plat'] ?? '');
            $type = $_POST['type_plat'] ?? '';

            // Seuls les allergènes qui existent vraiment sont gardés
            $idsValides = array_map(fn($a) => $a->getAllergeneId(), $allergenes);
            $allergenesCoches = array_values(array_intersect(array_map('intval', $_POST['allergenes'] ?? []), $idsValides));

            if ($titre === '') {
                $errors[] = 'Le titre du plat est obligatoire.';
            }
            if (!in_array($type, ['entrée', 'plat', 'dessert'], true)) {
                $errors[] = 'Choisissez le type du plat.';
            }

            $photo = $this->traiterPhoto($errors);

            $plat->setTitrePlat($titre);
            $plat->setTypePlat($type);
            if ($photo !== null) {
                $plat->setPhoto($photo);
            }

            if (empty($errors)) {
                $platRepository->save($plat, $allergenesCoches);
                $_SESSION['message_succes'] = 'Le plat a bien été enregistré.';
                header('Location: index.php?controller=employe&action=plats');
                exit;
            }
        }

        $this->render('employe/plat_form', [
            'plat' => $plat,
            'allergenes' => $allergenes,
            'allergenesCoches' => $allergenesCoches,
            'errors' => $errors,
        ]);
    }

    /*
    Vérifie et enregistre la photo envoyée.
    Renvoie le nom du fichier, ou null s'il n'y a pas de nouvelle photo.
*/
    protected function traiterPhoto(array &$errors): ?string
    {
        if (empty($_FILES['photo']['name'])) {
            return null;
        }
        if ($_FILES['photo']['error'] === UPLOAD_ERR_INI_SIZE || $_FILES['photo']['error'] === UPLOAD_ERR_FORM_SIZE) {
            $errors[] = 'La photo est trop lourde : 2 Mo maximum.';
            return null;
        }
        if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'L\'envoi de la photo a échoué.';
            return null;
        }
        if ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'La photo ne doit pas dépasser 2 Mo.';
            return null;
        }

        // On vérifie le contenu réel du fichier, pas seulement son extension
        $typesAutorises = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $type = mime_content_type($_FILES['photo']['tmp_name']);
        if (!isset($typesAutorises[$type])) {
            $errors[] = 'Formats acceptés : JPEG, PNG ou WebP.';
            return null;
        }

        // Nouveau nom aléatoire : on n'utilise jamais le nom choisi par l'utilisateur
        $nom = 'plat-' . bin2hex(random_bytes(8)) . '.' . $typesAutorises[$type];
        $destination = _ROOTPATH_ . '/public/assets/images/plats/' . $nom;

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
            $errors[] = 'Impossible d\'enregistrer la photo.';
            return null;
        }

        return $nom;
    }

    protected function supprimerPlat()
    {
        $id = (int) ($_POST['plat_id'] ?? 0);
        $platRepository = new PlatRepository();

        if ($platRepository->estCommande($id)) {
            $_SESSION['message_erreur'] = 'Ce plat a déjà été commandé : il ne peut pas être supprimé.';
        } else {
            $platRepository->delete($id);
            $_SESSION['message_succes'] = 'Le plat a bien été supprimé.';
        }

        header('Location: index.php?controller=employe&action=plats');
        exit;
    }
}
