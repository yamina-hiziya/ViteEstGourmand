<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MenuRepository;
use App\Repository\PlatRepository;
use App\Repository\UserRepository;
use App\Repository\CommandeRepository;
use App\Entity\Commande;
use App\Tools\GeoTools;


class CommandeController extends Controller
{
    public function route(): void
    {
        try {
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'create':
                        $this->create();
                        break;
                    case 'confirm':
                        $this->confirm();
                        break;
                    case 'merci':
                        $this->merci();
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

    protected function create()
    {
        try {
            // 1. Seul un utilisateur connecté peut commander
            if (!User::isLogged()) {
                // On mémorise la page de commande, pour y revenir après la connexion
                $_SESSION['redirect_after_login'] = 'index.php?controller=commande&action=create&menu_id=' . (int) ($_GET['menu_id'] ?? 0);
                $_SESSION['message_succes'] = 'Connectez-vous ou créez un compte pour commander ce menu.';
                header('Location: index.php?controller=auth&action=login');
                exit;
            }

            // 2. On vérifie que l'id du menu est bien dans l'URL
            if (!isset($_GET['menu_id'])) {
                throw new \Exception("Le menu à commander n'est pas précisé");
            }

            // 3. On récupère le menu, comme dans MenuController::show()
            $menuRepository = new MenuRepository();
            $id = (int) $_GET['menu_id'];
            $menu = $menuRepository->findOneById($id);

            if (!$menu) {
                throw new \Exception("Ce menu n'existe pas");
            }

            // 4. On récupère ses plats, pour que le client puisse choisir
            $platRepository = new PlatRepository();
            $plats = $platRepository->findAllByMenuId($id);

            // On récupère l'utilisateur connecté, pour pré-remplir ses informations
            $userRepository = new UserRepository();
            $user = $userRepository->findOneById(User::getCurrentUserId());

            $errors = [];

            // Le formulaire a-t-il été envoyé ?
            if (isset($_POST['saveCommande'])) {

                // On récupère et nettoie les valeurs
                $adresse = trim($_POST['adresse_livraison'] ?? '');
                $ville = trim($_POST['ville_livraison'] ?? '');
                $date = $_POST['date_prestation'] ?? '';
                $heure = $_POST['heure_livraison'] ?? '';
                $nbPersonnes = (int) ($_POST['nombre_personne'] ?? 0);
                $quantites = $_POST['quantites'] ?? [];

                // Adresse, ville et heure
                if ($adresse === '' || $ville === '') {
                    $errors[] = 'Merci d\'indiquer l\'adresse et la ville de la prestation.';
                }
                if ($heure === '') {
                    $errors[] = 'Merci d\'indiquer l\'heure de livraison souhaitée.';
                }

                // Date : valide, et au plus tôt demain
                $datePrestation = \DateTime::createFromFormat('Y-m-d', $date);
                $demain = new \DateTime('tomorrow');
                if (!$datePrestation || $datePrestation < $demain) {
                    $errors[] = 'La date de prestation doit être au plus tôt demain.';
                }

                // Nombre de personnes et stock
                if ($nbPersonnes < $menu->getNombrePersonneMinimum()) {
                    $errors[] = 'Ce menu se commande pour ' . $menu->getNombrePersonneMinimum() . ' personnes minimum.';
                }
                if ($menu->getQuantiteRestante() <= 0) {
                    $errors[] = 'Ce menu n\'est plus disponible pour le moment.';
                }

                // Choix des plats : pour chaque type, la somme doit égaler le nombre de personnes
                $platsParType = [];
                foreach ($plats as $plat) {
                    $platsParType[$plat->getTypePlat()][] = $plat;
                }

                $lignes = [];
                foreach ($platsParType as $type => $platsDuType) {
                    $total = 0;
                    foreach ($platsDuType as $plat) {
                        $quantite = max(0, (int) ($quantites[$plat->getPlatId()] ?? 0));
                        $total += $quantite;
                        if ($quantite > 0) {
                            $lignes[] = [
                                'plat_id' => $plat->getPlatId(),
                                'titre' => $plat->getTitrePlat(),
                                'type' => $type,
                                'quantite' => $quantite,
                            ];
                        }
                    }
                    if ($total !== $nbPersonnes) {
                        $errors[] = 'Pour les ' . $type . 's, la somme des quantités (' . $total . ') doit être égale au nombre de personnes (' . $nbPersonnes . ').';
                    }
                }

                // Distance : on interroge l'API, seulement si l'adresse est remplie
                $point = null;
                if ($adresse !== '' && $ville !== '') {
                    $point = GeoTools::geocoder($adresse . ' ' . $ville);
                    if ($point === null) {
                        $errors[] = 'Adresse introuvable. Merci de vérifier l\'adresse et la ville.';
                    }
                }

                // Aucune erreur : on calcule le prix et on prépare le récapitulatif
                if (empty($errors)) {
                    $distance = round(GeoTools::distanceKm(GeoTools::BORDEAUX_LAT, GeoTools::BORDEAUX_LON, $point['lat'], $point['lon']), 2);
                    $prixSansReduction = round($menu->getPrixParPersonne() * $nbPersonnes, 2);
                    $prixMenu = Commande::calculerPrixMenu($menu->getPrixParPersonne(), $nbPersonnes, $menu->getNombrePersonneMinimum());
                    $prixLivraison = Commande::calculerFraisLivraison($point['ville'], $distance);

                    // On garde tout en session : le client ne pourra pas modifier ces valeurs
                    $_SESSION['commande_en_cours'] = [
                        'menu_id' => $menu->getMenuId(),
                        'adresse_livraison' => $adresse,
                        'ville_livraison' => $point['ville'],
                        'date_prestation' => $date,
                        'heure_livraison' => $heure,
                        'nombre_personne' => $nbPersonnes,
                        'distance_km' => $distance,
                        'prix_sans_reduction' => $prixSansReduction,
                        'prix_menu' => $prixMenu,
                        'prix_livraison' => $prixLivraison,
                        'lignes' => $lignes,
                    ];

                    $this->render('commande/recap', [
                        'menu' => $menu,
                        'commande' => $_SESSION['commande_en_cours'],
                    ]);
                    return;
                }
            }
            // 5. On affiche le formulaire de commande
            $this->render('commande/create', [
                'menu' => $menu,
                'plats' => $plats,
                'user' => $user,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function confirm()
    {
        try {
            if (!User::isLogged()) {
                header('Location: index.php?controller=auth&action=login');
                exit;
            }

            if (!isset($_POST['confirmCommande']) || empty($_SESSION['commande_en_cours'])) {
                throw new \Exception('Aucune commande en cours.');
            }

            $commandeRepository = new CommandeRepository();
            $numero = $commandeRepository->creer($_SESSION['commande_en_cours'], User::getCurrentUserId());

            // La commande est enregistrée : on vide la session et on redirige
            unset($_SESSION['commande_en_cours']);
            $_SESSION['commande_confirmee'] = $numero;
            header('Location: index.php?controller=commande&action=merci');
            exit;
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function merci()
    {
        $numero = $_SESSION['commande_confirmee'] ?? null;
        unset($_SESSION['commande_confirmee']);

        if (!$numero) {
            header('Location: index.php?controller=menu&action=list');
            exit;
        }

        $this->render('commande/merci', [
            'numero' => $numero,
        ]);
    }
}
