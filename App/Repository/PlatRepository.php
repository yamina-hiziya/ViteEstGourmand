<?php

namespace App\Repository;

use App\Entity\Plat;

class PlatRepository extends Repository
{
    // Récupère les plats d'un menu grâce à la table menu_plat
    public function findAllByMenuId(int $menu_id): array
    {
        $query = $this->pdo->prepare("SELECT p.*, GROUP_CONCAT(a.libelle SEPARATOR ', ') AS allergenes
                            FROM plat p
                            JOIN menu_plat mp ON mp.plat_id = p.plat_id
                            LEFT JOIN plat_allergene pa ON pa.plat_id = p.plat_id
                            LEFT JOIN allergene a ON a.allergene_id = pa.allergene_id
                            WHERE mp.menu_id = :menu_id
                            GROUP BY p.plat_id
                            ORDER BY FIELD(p.type_plat, 'entrée', 'plat', 'dessert')");
        $query->bindParam(':menu_id', $menu_id, $this->pdo::PARAM_INT);
        $query->execute();
        $plats = $query->fetchAll($this->pdo::FETCH_ASSOC);

        $platsArray = [];

        if ($plats) {
            foreach ($plats as $plat) {
                $platsArray[] = Plat::createAndHydrate($plat);
            }
        }
        return $platsArray;
    }
}