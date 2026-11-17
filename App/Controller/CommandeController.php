<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MenuRepository;
use App\Repository\PlatRepository;
use App\Repository\UserRepository;
use App\Repository\CommandeRepository;
use App\Repository\StatistiqueRepository;
use App\Entity\Commande;
use App\Tools\GeoTools;
use App\Tools\Mailer;


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
                    case 'modifier':
                        $this->modifier();
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

            if (isset($_POST['saveCommande'])) {
                $resultat = $this->traiterFormulaire($menu, $plats);
                $errors = $resultat['errors'];

                if (empty($errors)) {
                    $_SESSION['commande_en_cours'] = $resultat['commande'];
                    $this->render('commande/recap', [
                        'menu' => $menu,
                        'commande' => $resultat['commande'],
                    ]);
                    return;
                }
            }

            $this->render('commande/create', [
                'menu' => $menu,
                'plats' => $plats,
                'user' => $user,
                'errors' => $errors,
                'valeurs' => $_POST,
                'numero' => null,
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

            // S'il y a un numéro en session, c'est une modification
            if (!empty($_SESSION['commande_en_cours']['numero_commande'])) {
                $numero = $_SESSION['commande_en_cours']['numero_commande'];
                $commandeRepository->modifier($numero, User::getCurrentUserId(), $_SESSION['commande_en_cours']);
                $this->enregistrerStatistique($numero, $_SESSION['commande_en_cours']);

                unset($_SESSION['commande_en_cours']);
                $_SESSION['message_succes'] = 'Votre commande a bien été modifiée.';
                header('Location: index.php?controller=compte&action=detail&numero=' . urlencode($numero));
                exit;
            }

            // Sinon, c'est une nouvelle commande
            $numero = $commandeRepository->creer($_SESSION['commande_en_cours'], User::getCurrentUserId());
            $this->enregistrerStatistique($numero, $_SESSION['commande_en_cours']);

            // Mail de confirmation au client
            $cmd = $_SESSION['commande_en_cours'];
            Mailer::envoyer(
                $_SESSION['user']['email'],
                'Confirmation de votre commande ' . $numero,
                '<p>Bonjour ' . htmlspecialchars(ucfirst($_SESSION['user']['prenom'])) . ',</p>
             <p>Nous avons bien reçu votre commande <strong>' . htmlspecialchars($numero) . '</strong>.</p>
             <ul>
                <li>Menu : ' . htmlspecialchars($cmd['menu_titre']) . '</li>
                <li>Date : ' . date('d/m/Y', strtotime($cmd['date_prestation'])) . ' à ' . htmlspecialchars($cmd['heure_livraison']) . '</li>
                <li>Nombre de personnes : ' . (int) $cmd['nombre_personne'] . '</li>
                <li>Total : ' . number_format($cmd['prix_menu'] + $cmd['prix_livraison'], 2, ',', ' ') . ' €</li>
             </ul>
             <p>Elle est en attente de validation par notre équipe. Vous pouvez suivre son avancement depuis votre espace.</p>'
            );

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

    /*
    Vérifie le formulaire de commande et calcule le prix.
    Renvoie ['errors' => [...], 'commande' => [...] ou null]
*/
    protected function traiterFormulaire($menu, array $plats, bool $verifierStock = true): array
    {
        $errors = [];

        $adresse = trim($_POST['adresse_livraison'] ?? '');
        $ville = trim($_POST['ville_livraison'] ?? '');
        $date = $_POST['date_prestation'] ?? '';
        $heure = $_POST['heure_livraison'] ?? '';
        $nbPersonnes = (int) ($_POST['nombre_personne'] ?? 0);
        $quantites = $_POST['quantites'] ?? [];

        if ($adresse === '' || $ville === '') {
            $errors[] = 'Merci d\'indiquer l\'adresse et la ville de la prestation.';
        }
        if ($heure === '') {
            $errors[] = 'Merci d\'indiquer l\'heure de livraison souhaitée.';
        }

        $datePrestation = \DateTime::createFromFormat('Y-m-d', $date);
        $demain = new \DateTime('tomorrow');
        if (!$datePrestation || $datePrestation < $demain) {
            $errors[] = 'La date de prestation doit être au plus tôt demain.';
        }

        if ($nbPersonnes < $menu->getNombrePersonneMinimum()) {
            $errors[] = 'Ce menu se commande pour ' . $menu->getNombrePersonneMinimum() . ' personnes minimum.';
        }
        if ($verifierStock && $menu->getQuantiteRestante() <= 0) {
            $errors[] = 'Ce menu n\'est plus disponible pour le moment.';
        }

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

        $point = null;
        if ($adresse !== '' && $ville !== '') {
            $point = GeoTools::geocoder($adresse . ' ' . $ville);
            if ($point === null) {
                $errors[] = 'Adresse introuvable. Merci de vérifier l\'adresse et la ville.';
            }
        }

        if (!empty($errors)) {
            return ['errors' => $errors, 'commande' => null];
        }

        $distance = round(GeoTools::distanceKm(GeoTools::BORDEAUX_LAT, GeoTools::BORDEAUX_LON, $point['lat'], $point['lon']), 2);

        return [
            'errors' => [],
            'commande' => [
                'menu_id' => $menu->getMenuId(),
                'menu_titre' => $menu->getTitre(),
                'adresse_livraison' => $adresse,
                'ville_livraison' => $point['ville'],
                'date_prestation' => $date,
                'heure_livraison' => $heure,
                'nombre_personne' => $nbPersonnes,
                'distance_km' => $distance,
                'prix_sans_reduction' => round($menu->getPrixParPersonne() * $nbPersonnes, 2),
                'prix_menu' => Commande::calculerPrixMenu($menu->getPrixParPersonne(), $nbPersonnes, $menu->getNombrePersonneMinimum()),
                'prix_livraison' => Commande::calculerFraisLivraison($point['ville'], $distance),
                'lignes' => $lignes,
            ],
        ];
    }
    protected function modifier()
    {
        try {
            if (!User::isLogged()) {
                header('Location: index.php?controller=auth&action=login');
                exit;
            }

            // La commande doit appartenir à l'utilisateur et être encore en attente
            $numero = $_GET['numero'] ?? '';
            $commandeRepository = new CommandeRepository();
            $existante = $commandeRepository->findOneByNumeroForUser($numero, User::getCurrentUserId());
            if (!$existante || $existante['statut'] !== 'en attente') {
                throw new \Exception('Cette commande ne peut plus être modifiée.');
            }

            // Le menu ne change pas : on reprend celui de la commande
            $menuId = (int) $existante['menu_id'];
            $menu = (new MenuRepository())->findOneById($menuId);
            $plats = (new PlatRepository())->findAllByMenuId($menuId);
            $user = (new UserRepository())->findOneById(User::getCurrentUserId());
            $errors = [];

            if (isset($_POST['saveCommande'])) {
                $valeurs = $_POST;
                // Le stock n'est pas revérifié : la commande occupe déjà sa place
                $resultat = $this->traiterFormulaire($menu, $plats, false);
                $errors = $resultat['errors'];

                if (empty($errors)) {
                    $resultat['commande']['numero_commande'] = $numero;
                    $_SESSION['commande_en_cours'] = $resultat['commande'];
                    $this->render('commande/recap', [
                        'menu' => $menu,
                        'commande' => $resultat['commande'],
                    ]);
                    return;
                }
            } else {
                // Premier affichage : on reprend les valeurs de la commande existante
                $valeurs = [
                    'adresse_livraison' => $existante['adresse_livraison'],
                    'ville_livraison' => $existante['ville_livraison'],
                    'date_prestation' => $existante['date_prestation'],
                    'heure_livraison' => substr($existante['heure_livraison'], 0, 5),
                    'nombre_personne' => $existante['nombre_personne'],
                    'quantites' => [],
                ];
                foreach ($commandeRepository->findLignes($numero) as $ligne) {
                    $valeurs['quantites'][$ligne['plat_id']] = $ligne['quantite'];
                }
            }

            $this->render('commande/create', [
                'menu' => $menu,
                'plats' => $plats,
                'user' => $user,
                'errors' => $errors,
                'valeurs' => $valeurs,
                'numero' => $numero,
            ]);
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /*
    Copie la commande dans MongoDB pour les statistiques.
    Si MongoDB ne répond pas, la commande reste enregistrée dans MySQL.
*/
    protected function enregistrerStatistique(string $numero, array $commande): void
    {
        try {
            (new StatistiqueRepository())->enregistrerCommande(
                $numero,
                (int) $commande['menu_id'],
                $commande['menu_titre'],
                (float) $commande['prix_menu'] + (float) $commande['prix_livraison'],
                'en attente'
            );
        } catch (\Exception $e) {
            // MySQL reste la référence : les statistiques pourront être resynchronisées
        }
    }
}
