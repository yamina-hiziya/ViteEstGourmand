<?php

namespace App\Repository;
use App\Entity\Menu;

class MenuRepository extends Repository
{
    public function findOneById(int $id): Menu|bool
    {
        $query = $this->pdo->prepare("SELECT menu.*, theme.libelle 
                                    AS theme_libelle, regime.libelle 
                                    AS regime_libelle
                                    FROM menu
                                    JOIN theme ON menu.theme_id = theme.theme_id
                                    JOIN regime ON menu.regime_id = regime.regime_id 
                                    WHERE menu_id = :id");
        $query->bindParam(':id', $id, $this->pdo::PARAM_INT);
        $query->execute();
        $menuData = $query->fetch($this->pdo::FETCH_ASSOC);
        if ($menuData) {
            return Menu::createAndHydrate($menuData);
        } else {
            return false;
        }
    }

    public function findAll(): array
    {
        $query = $this->pdo->prepare("SELECT * FROM menu ORDER BY titre");
        $query->execute();
        $menus = $query->fetchAll($this->pdo::FETCH_ASSOC);

        $menusArray = [];

        if ($menus) {
            foreach ($menus as $menu) {
                $menusArray[] = Menu::createAndHydrate($menu);
            }
        }
        return $menusArray;
    }

    //je prepare la requete pour gerer les filtres
    public function findByFilters(array $filtres): array{

        //je prepare 2 tableau vides
        $conditions = [];
        $params = [];
        // pour chaque filtre rempli on ajoute une conditions
        if (!empty($filtres['prix_min'])) {
            $conditions[] = 'menu.prix_par_personne >= :prix_min';
            $params['prix_min'] = (float) $filtres['prix_min'];
        }
        if (!empty($filtres['prix_max'])) {
            $conditions[] = 'menu.prix_par_personne <= :prix_max';
            $params['prix_max'] = (float) $filtres['prix_max'];
        }
        if (!empty($filtres['theme_id'])) {
            $conditions[] = 'menu.theme_id = :theme_id';
            $params['theme_id'] = (int) $filtres['theme_id'];
        }
        if (!empty($filtres['regime_id'])) {
            $conditions[] = 'menu.regime_id = :regime_id';
            $params['regime_id'] = (int) $filtres['regime_id'];
        }
        if (!empty($filtres['nb_personnes'])) {
            $conditions[] = 'menu.nombre_personne_minimum <= :nb_personnes';
            $params['nb_personnes'] = (int) $filtres['nb_personnes'];
        }
        //on ecrit la requetes sans le where
        $sql = "SELECT menu.*, theme.libelle 
                                    AS theme_libelle, regime.libelle 
                                    AS regime_libelle
                                    FROM menu
                                    JOIN theme ON menu.theme_id = theme.theme_id
                                    JOIN regime ON menu.regime_id = regime.regime_id";

        //si il y a des conditions on les ajoutes a la requetes
        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY menu.titre';

        //on execute la requetes avec les parametres
        $query = $this->pdo->prepare($sql);
        $query->execute($params);
        $menus = $query->fetchAll($this->pdo::FETCH_ASSOC);

        $menusArray = [];
        foreach ($menus as $menu) {
            $menusArray[] = Menu::createAndHydrate($menu);
        }
        return $menusArray;
    }
}