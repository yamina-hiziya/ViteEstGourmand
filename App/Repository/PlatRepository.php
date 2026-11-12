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

    /*
    Tous les plats, avec leurs allergènes, pour l'espace employé
*/
    public function findAllAvecAllergenes(): array
    {
        $query = $this->pdo->prepare('SELECT p.*, GROUP_CONCAT(a.libelle SEPARATOR \', \') AS allergenes
                                  FROM plat p
                                  LEFT JOIN plat_allergene pa ON pa.plat_id = p.plat_id
                                  LEFT JOIN allergene a ON a.allergene_id = pa.allergene_id
                                  GROUP BY p.plat_id
                                  ORDER BY FIELD(p.type_plat, \'entrée\', \'plat\', \'dessert\'), p.titre_plat');
        $query->execute();
        $plats = [];
        foreach ($query->fetchAll($this->pdo::FETCH_ASSOC) as $plat) {
            $plats[] = Plat::createAndHydrate($plat);
        }
        return $plats;
    }

    public function findOneById(int $id): Plat|bool
    {
        $query = $this->pdo->prepare('SELECT * FROM plat WHERE plat_id = :id');
        $query->execute(['id' => $id]);
        $plat = $query->fetch($this->pdo::FETCH_ASSOC);
        return $plat ? Plat::createAndHydrate($plat) : false;
    }

    /*
    Les identifiants des allergènes d'un plat, pour cocher les bonnes cases
*/
    public function findAllergeneIds(int $platId): array
    {
        $query = $this->pdo->prepare('SELECT allergene_id FROM plat_allergene WHERE plat_id = :id');
        $query->execute(['id' => $platId]);
        return $query->fetchAll($this->pdo::FETCH_COLUMN);
    }

    /*
    Création ou modification d'un plat et de ses allergènes, dans une transaction
*/
    public function save(Plat $plat, array $allergeneIds): int
    {
        try {
            $this->pdo->beginTransaction();

            $donnees = [
                'titre' => $plat->getTitrePlat(),
                'photo' => $plat->getPhoto() ?? '',
                'type' => $plat->getTypePlat(),
            ];

            if ($plat->getPlatId()) {
                $query = $this->pdo->prepare('UPDATE plat SET titre_plat = :titre, photo = :photo, type_plat = :type
                                          WHERE plat_id = :id');
                $query->execute($donnees + ['id' => $plat->getPlatId()]);
                $id = $plat->getPlatId();
            } else {
                $query = $this->pdo->prepare('INSERT INTO plat (titre_plat, photo, type_plat) VALUES (:titre, :photo, :type)');
                $query->execute($donnees);
                $id = (int) $this->pdo->lastInsertId();
            }

            // On remplace les allergènes du plat
            $query = $this->pdo->prepare('DELETE FROM plat_allergene WHERE plat_id = :id');
            $query->execute(['id' => $id]);

            $query = $this->pdo->prepare('INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (:plat_id, :allergene_id)');
            foreach ($allergeneIds as $allergeneId) {
                $query->execute(['plat_id' => $id, 'allergene_id' => $allergeneId]);
            }

            $this->pdo->commit();
            return $id;
        } catch (\Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    /*
    Un plat déjà commandé ne doit pas être supprimé
*/
    public function estCommande(int $platId): bool
    {
        $query = $this->pdo->prepare('SELECT COUNT(*) FROM commande_plat WHERE plat_id = :id');
        $query->execute(['id' => $platId]);
        return $query->fetchColumn() > 0;
    }

    public function delete(int $platId): void
    {
        $query = $this->pdo->prepare('DELETE FROM plat WHERE plat_id = :id');
        $query->execute(['id' => $platId]);
    }
}
