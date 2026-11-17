<?php

namespace App\Repository;
use App\Entity\Regime;

class RegimeRepository extends Repository
{
    public function findAll(): array
    {
        $query = $this->pdo->prepare("SELECT * FROM regime ORDER BY libelle");
        $query->execute();
        $regimes = $query->fetchAll($this->pdo::FETCH_ASSOC);

        $regimesArray = [];

        if ($regimes) {
            foreach ($regimes as $regime) {
                $regimesArray[] = Regime::createAndHydrate($regime);
            }
        }
        return $regimesArray;
    }
}