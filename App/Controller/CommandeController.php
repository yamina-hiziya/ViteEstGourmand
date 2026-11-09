<?php

namespace App\Controller;
use App\Entity\User;
use App\Repository\MenuRepository;
use App\Repository\PlatRepository;


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

            // 5. On affiche le formulaire de commande
            $this->render('commande/create', [
                'menu' => $menu,
                'plats' => $plats,
            ]);
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
