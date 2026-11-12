<?php

namespace App\Repository;

use App\Entity\Avis;

class AvisRepository extends Repository
{
    public function findOneByNumeroCommande(string $numero): Avis|bool
    {
        $query = $this->pdo->prepare('SELECT * FROM avis WHERE numero_commande = :numero');
        $query->execute(['numero' => $numero]);
        $avis = $query->fetch($this->pdo::FETCH_ASSOC);
        return $avis ? Avis::createAndHydrate($avis) : false;
    }

    public function creer(int $note, string $description, string $numero, int $utilisateurId): bool
    {
        $query = $this->pdo->prepare('INSERT INTO avis (note, description, statut, numero_commande, utilisateur_id)
                                      VALUES (:note, :description, \'en attente\', :numero, :utilisateur_id)');
        return $query->execute([
            'note' => $note,
            'description' => $description,
            'numero' => $numero,
            'utilisateur_id' => $utilisateurId,
        ]);
    }
}