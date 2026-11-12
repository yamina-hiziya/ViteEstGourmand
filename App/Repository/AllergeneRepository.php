<?php

namespace App\Repository;

use App\Entity\Allergene;

class AllergeneRepository extends Repository
{
    public function findAll(): array
    {
        $query = $this->pdo->prepare('SELECT * FROM allergene ORDER BY libelle');
        $query->execute();
        $allergenes = [];
        foreach ($query->fetchAll($this->pdo::FETCH_ASSOC) as $allergene) {
            $allergenes[] = Allergene::createAndHydrate($allergene);
        }
        return $allergenes;
    }
}