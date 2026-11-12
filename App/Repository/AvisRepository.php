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

    /*
    Les avis à modérer, avec le prénom du client et le menu
*/
    public function findEnAttente(): array
    {
        $query = $this->pdo->prepare('SELECT a.avis_id, a.note, a.description, u.prenom, m.titre AS menu_titre
                                  FROM avis a
                                  JOIN utilisateur u ON u.utilisateur_id = a.utilisateur_id
                                  JOIN commande c ON c.numero_commande = a.numero_commande
                                  JOIN menu m ON m.menu_id = c.menu_id
                                  WHERE a.statut = \'en attente\'
                                  ORDER BY a.avis_id ASC');
        $query->execute();
        return $query->fetchAll($this->pdo::FETCH_ASSOC);
    }

    /*
    Validation ou refus d'un avis encore en attente
*/
    public function changerStatut(int $avisId, string $statut): bool
    {
        $query = $this->pdo->prepare('UPDATE avis SET statut = :statut
                                  WHERE avis_id = :avis_id AND statut = \'en attente\'');
        $query->execute(['statut' => $statut, 'avis_id' => $avisId]);
        return $query->rowCount() > 0;
    }

    /*
    Les derniers avis validés, pour la page d'accueil
*/
    public function findValides(int $limite): array
    {
        $query = $this->pdo->prepare('SELECT a.note, a.description, u.prenom
                                  FROM avis a
                                  JOIN utilisateur u ON u.utilisateur_id = a.utilisateur_id
                                  WHERE a.statut = \'validé\'
                                  ORDER BY a.avis_id DESC
                                  LIMIT :limite');
        $query->bindValue(':limite', $limite, $this->pdo::PARAM_INT);
        $query->execute();
        return $query->fetchAll($this->pdo::FETCH_ASSOC);
    }
}
