<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use App\Repository\PlatRepository;
use App\Repository\ThemeRepository;
use App\Repository\RegimeRepository;

class MenuController extends Controller
{
    public function route(): void
    {
        try {
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'show':
                        $this->show();
                        break;
                    case 'list':
                        $this->list();
                        break;
                    case 'filter':
                        $this->filter();
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

    protected function show()
    {
        try {
            if (!isset($_GET['id'])) {
                throw new \Exception("L'id est manquant en paramètre d'url");
            }

            $menuRepository = new MenuRepository();
            $id = (int) $_GET['id'];
            $menu = $menuRepository->findOneById($id);

            if (!$menu) {
                throw new \Exception("Ce menu n'existe pas");
            }
            $platRepository = new PlatRepository();
            $plats = $platRepository->findAllByMenuId($id);

            $this->render('menus/menu', [
                'menu' => $menu,
                'plats' => $plats,
            ]);
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }


    protected function list()
    {
        try {
            // Récupérer tous les menus
            $menuRepository = new MenuRepository();
            // Si un thème est demandé dans l'URL (depuis l'accueil), on filtre dès l'arrivée
            $themeChoisi = (int) ($_GET['theme_id'] ?? 0);
            $menus = $menuRepository->findByFilters(['theme_id' => $themeChoisi ?: null]);
            // Récupérer tous les thèmes et allergènes
            $themeRepository = new ThemeRepository();
            $regimeRepository = new RegimeRepository();
            $themes = $themeRepository->findAll();
            $regimes = $regimeRepository->findAll();


            $this->render('menus/list', [
                'menus' => $menus,
                'themes' => $themes,
                'regimes' => $regimes,
                'themeChoisi' => $themeChoisi,
            ]);
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function filter()
    {
        try {
            // Récupérer tous les menus
            $menuRepository = new MenuRepository();
            $menus = $menuRepository->findByFilters($_GET);


            $this->render('menus/_cards', [
                'menus' => $menus,
            ]);
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
