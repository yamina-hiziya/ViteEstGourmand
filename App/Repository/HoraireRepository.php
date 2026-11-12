<?php

namespace App\Repository;

use App\Entity\Horaire;

class HoraireRepository extends Repository
{
    public function findAll(): array
    {
        $query = $this->pdo->prepare('SELECT * FROM horaire ORDER BY horaire_id');
        $query->execute();
        $horaires = [];
        foreach ($query->fetchAll($this->pdo::FETCH_ASSOC) as $horaire) {
            $horaires[] = Horaire::createAndHydrate($horaire);
        }
        return $horaires;
    }

    /*
        Enregistre tous les horaires d'un coup, dans une transaction.
        $horaires = [horaire_id => ['ouverture' => '09:00' ou null, 'fermeture' => '18:00' ou null]]
    */
    public function updateAll(array $horaires): void
    {
        try {
            $this->pdo->beginTransaction();
            $query = $this->pdo->prepare('UPDATE horaire SET heure_ouverture = :ouverture, heure_fermeture = :fermeture
                                          WHERE horaire_id = :id');
            foreach ($horaires as $id => $heures) {
                $query->execute([
                    'ouverture' => $heures['ouverture'],
                    'fermeture' => $heures['fermeture'],
                    'id' => $id,
                ]);
            }
            $this->pdo->commit();
        } catch (\Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}
