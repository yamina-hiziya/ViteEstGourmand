<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\User;
use App\Entity\Plat;
use App\Entity\Menu;
use App\Repository\CommandeRepository;
use App\Repository\AvisRepository;
use App\Repository\HoraireRepository;
use App\Repository\PlatRepository;
use App\Repository\AllergeneRepository;
use App\Repository\MenuRepository;
use App\Repository\ThemeRepository;
use App\Repository\RegimeRepository;
use App\Repository\StatistiqueRepository;
use App\Tools\Mailer;

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
                    case 'menus':
                        $this->menus();
                        break;
                    case 'menu':
                        $this->menu();
                        break;
                    case 'supprimerMenu':
                        $this->supprimerMenu();
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
        try {
            (new StatistiqueRepository())->changerStatut($numero, $statut);
        } catch (\Exception $e) {
            // MySQL reste la référence
        }
        // Mails au client selon le nouveau statut
        if (in_array($statut, ['en attente du retour de matériel', 'terminée'], true)) {
            $commande = $commandeRepository->findOneByNumeroForEmploye($numero);
            $prenom = htmlspecialchars(ucfirst($commande['prenom']));
            $numeroHtml = htmlspecialchars($numero);

            if ($statut === 'en attente du retour de matériel') {
                Mailer::envoyer(
                    $commande['email'],
                    'Restitution du matériel — commande ' . $numero,
                    '<p>Bonjour ' . $prenom . ',</p>
                <p>Du matériel vous a été prêté pour votre commande <strong>' . $numeroHtml . '</strong>.</p>
                <p>Merci de le restituer sous <strong>10 jours ouvrés</strong>. Passé ce délai, des frais de <strong>600 €</strong> vous seront facturés, conformément à nos conditions générales de vente.</p>
                <p>Pour organiser le retour du matériel, merci de reprendre contact avec nous.</p>'
                );
            }

            if ($statut === 'terminée') {
                $lien = Mailer::url('index.php?controller=compte&action=detail&numero=' . urlencode($numero));
                Mailer::envoyer(
                    $commande['email'],
                    'Votre avis nous intéresse — commande ' . $numero,
                    '<p>Bonjour ' . $prenom . ',</p>
                <p>Votre commande <strong>' . $numeroHtml . '</strong> est terminée. Nous espérons que vous et vos invités vous êtes régalés !</p>
                <p>Connectez-vous à votre compte pour nous laisser une note et un commentaire :</p>
                <p><a href="' . htmlspecialchars($lien) . '" style="background: #1B4A8A; color: #ffffff; padding: 10px 16px; border-radius: 6px; text-decoration: none;">Donner mon avis</a></p>'
                );
            }
        }

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

            $photo = $this->traiterPhoto($errors, 'plats', 'plat');
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
    protected function traiterPhoto(array &$errors, string $dossier, string $prefixe): ?string
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
        $nom = $prefixe . '-' . bin2hex(random_bytes(8)) . '.' . $typesAutorises[$type];
        $destination = _ROOTPATH_ . '/public/assets/images/' . $dossier . '/' . $nom;

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

    protected function menus()
    {
        $succes = $_SESSION['message_succes'] ?? null;
        $erreur = $_SESSION['message_erreur'] ?? null;
        unset($_SESSION['message_succes'], $_SESSION['message_erreur']);

        $this->render('employe/menus', [
            'menus' => (new MenuRepository())->findByFilters([]),
            'succes' => $succes,
            'erreur' => $erreur,
        ]);
    }

    protected function menu()
    {
        $menuRepository = new MenuRepository();
        $themes = (new ThemeRepository())->findAll();
        $regimes = (new RegimeRepository())->findAll();
        $plats = (new PlatRepository())->findAllAvecAllergenes();
        $id = (int) ($_GET['id'] ?? 0);

        if ($id > 0) {
            $menu = $menuRepository->findOneById($id);
            if (!$menu) {
                throw new \Exception('Menu introuvable.');
            }
            $platsCoches = $menuRepository->findPlatIds($id);
        } else {
            $menu = new Menu();
            $platsCoches = [];
        }

        $errors = [];

        if (isset($_POST['saveMenu'])) {
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $conditions = trim($_POST['conditions'] ?? '');
            $minimum = (int) ($_POST['nombre_personne_minimum'] ?? 0);
            $prix = (float) str_replace(',', '.', $_POST['prix_par_personne'] ?? '0');
            $stock = (int) ($_POST['quantite_restante'] ?? -1);
            $themeId = (int) ($_POST['theme_id'] ?? 0);
            $regimeId = (int) ($_POST['regime_id'] ?? 0);

            // Seuls les plats qui existent vraiment sont gardés
            $platsParId = [];
            foreach ($plats as $plat) {
                $platsParId[$plat->getPlatId()] = $plat;
            }
            $platsCoches = array_values(array_intersect(array_map('intval', $_POST['plats'] ?? []), array_keys($platsParId)));

            if ($titre === '' || $description === '') {
                $errors[] = 'Le titre et la description sont obligatoires.';
            }
            if ($minimum < 1) {
                $errors[] = 'Le nombre de personnes minimum doit être d\'au moins 1.';
            }
            if ($prix <= 0) {
                $errors[] = 'Le prix par personne doit être supérieur à 0.';
            }
            if ($stock < 0) {
                $errors[] = 'Le stock doit être de 0 ou plus.';
            }
            if (!in_array($themeId, array_map(fn($t) => $t->getThemeId(), $themes), true)) {
                $errors[] = 'Choisissez un thème.';
            }
            if (!in_array($regimeId, array_map(fn($r) => $r->getRegimeId(), $regimes), true)) {
                $errors[] = 'Choisissez un régime.';
            }

            // Au moins une entrée, un plat et un dessert
            $typesCoches = array_map(fn($pid) => $platsParId[$pid]->getTypePlat(), $platsCoches);
            foreach (['entrée', 'plat', 'dessert'] as $type) {
                if (!in_array($type, $typesCoches, true)) {
                    $errors[] = 'Le menu doit proposer au moins un choix de type « ' . $type . ' ».';
                }
            }

            $photo = $this->traiterPhoto($errors, 'menu', 'menu');

            $menu->setTitre($titre);
            $menu->setDescription($description);
            $menu->setConditions($conditions);
            $menu->setNombrePersonneMinimum($minimum);
            $menu->setPrixParPersonne($prix);
            $menu->setQuantiteRestante($stock);
            $menu->setThemeId($themeId);
            $menu->setRegimeId($regimeId);
            if ($photo !== null) {
                $menu->setPhoto($photo);
            }

            if (empty($errors)) {
                $menuRepository->save($menu, $platsCoches);
                $_SESSION['message_succes'] = 'Le menu a bien été enregistré.';
                header('Location: index.php?controller=employe&action=menus');
                exit;
            }
        }

        $this->render('employe/menu_form', [
            'menu' => $menu,
            'themes' => $themes,
            'regimes' => $regimes,
            'plats' => $plats,
            'platsCoches' => $platsCoches,
            'errors' => $errors,
        ]);
    }

    protected function supprimerMenu()
    {
        $id = (int) ($_POST['menu_id'] ?? 0);
        $menuRepository = new MenuRepository();

        if ($menuRepository->estCommande($id)) {
            $_SESSION['message_erreur'] = 'Ce menu a déjà été commandé : il ne peut pas être supprimé. Pour le retirer de la vente, mettez son stock à 0.';
        } else {
            $menuRepository->delete($id);
            $_SESSION['message_succes'] = 'Le menu a bien été supprimé.';
        }

        header('Location: index.php?controller=employe&action=menus');
        exit;
    }
}
