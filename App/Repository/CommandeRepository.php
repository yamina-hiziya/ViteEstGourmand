<?php

namespace App\Repository;

class CommandeRepository extends Repository
{
    /*
        Enregistre une commande complète, dans une transaction.
        Renvoie le numéro de la commande créée.
    */
    public function creer(array $commande, int $utilisateurId): string
    {
        // Numéro unique, par exemple CMD-20261110-A1B2C3
        $numero = 'CMD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        try {
            $this->pdo->beginTransaction();

            // 1. On baisse le stock, seulement s'il en reste
            $query = $this->pdo->prepare('UPDATE menu SET quantite_restante = quantite_restante - 1
                                          WHERE menu_id = :menu_id AND quantite_restante > 0');
            $query->execute(['menu_id' => $commande['menu_id']]);
            if ($query->rowCount() === 0) {
                throw new \Exception('Ce menu n\'est plus disponible.');
            }

            // 2. On crée la commande
            $query = $this->pdo->prepare('INSERT INTO commande
                (numero_commande, date_commande, date_prestation, heure_livraison, adresse_livraison, ville_livraison,
                 distance_km, prix_menu, nombre_personne, prix_livraison, statut, utilisateur_id, menu_id)
                VALUES
                (:numero, NOW(), :date_prestation, :heure_livraison, :adresse_livraison, :ville_livraison,
                 :distance_km, :prix_menu, :nombre_personne, :prix_livraison, \'en attente\', :utilisateur_id, :menu_id)');
            $query->execute([
                'numero' => $numero,
                'date_prestation' => $commande['date_prestation'],
                'heure_livraison' => $commande['heure_livraison'],
                'adresse_livraison' => $commande['adresse_livraison'],
                'ville_livraison' => $commande['ville_livraison'],
                'distance_km' => $commande['distance_km'],
                'prix_menu' => $commande['prix_menu'],
                'nombre_personne' => $commande['nombre_personne'],
                'prix_livraison' => $commande['prix_livraison'],
                'utilisateur_id' => $utilisateurId,
                'menu_id' => $commande['menu_id'],
            ]);

            // 3. On enregistre les plats choisis
            $query = $this->pdo->prepare('INSERT INTO commande_plat (numero_commande, plat_id, quantite)
                                          VALUES (:numero, :plat_id, :quantite)');
            foreach ($commande['lignes'] as $ligne) {
                $query->execute([
                    'numero' => $numero,
                    'plat_id' => $ligne['plat_id'],
                    'quantite' => $ligne['quantite'],
                ]);
            }

            // 4. Premier état dans l'historique
            $query = $this->pdo->prepare('INSERT INTO suivi_commande (statut, date_heure, numero_commande)
                                          VALUES (\'en attente\', NOW(), :numero)');
            $query->execute(['numero' => $numero]);

            $this->pdo->commit();
            return $numero;
        } catch (\Exception $e) {
            // En cas d'erreur, on annule tout
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    /*
    Récupère toutes les commandes d'un utilisateur, avec le titre du menu
    */
    public function findAllByUtilisateurId(int $utilisateurId): array
    {
        $query = $this->pdo->prepare('SELECT c.numero_commande, c.date_commande, c.date_prestation, c.statut,
                                         c.prix_menu, c.prix_livraison, m.titre AS menu_titre
                                  FROM commande c
                                  JOIN menu m ON m.menu_id = c.menu_id
                                  WHERE c.utilisateur_id = :utilisateur_id
                                  ORDER BY c.date_commande DESC');
        $query->execute(['utilisateur_id' => $utilisateurId]);
        return $query->fetchAll($this->pdo::FETCH_ASSOC);
    }

    /*
    Une commande, seulement si elle appartient à l'utilisateur (sécurité)
*/
    public function findOneByNumeroForUser(string $numero, int $utilisateurId): array|bool
    {
        $query = $this->pdo->prepare('SELECT c.*, m.titre AS menu_titre, m.prix_par_personne
                                  FROM commande c
                                  JOIN menu m ON m.menu_id = c.menu_id
                                  WHERE c.numero_commande = :numero AND c.utilisateur_id = :utilisateur_id');
        $query->execute(['numero' => $numero, 'utilisateur_id' => $utilisateurId]);
        return $query->fetch($this->pdo::FETCH_ASSOC);
    }

    /*
    Les plats choisis dans une commande, avec leurs quantités
*/
    public function findLignes(string $numero): array
    {
        $query = $this->pdo->prepare('SELECT cp.plat_id, p.titre_plat, p.type_plat, cp.quantite
                                  FROM commande_plat cp
                                  JOIN plat p ON p.plat_id = cp.plat_id
                                  WHERE cp.numero_commande = :numero
                                  ORDER BY FIELD(p.type_plat, \'entrée\', \'plat\', \'dessert\')');
        $query->execute(['numero' => $numero]);
        return $query->fetchAll($this->pdo::FETCH_ASSOC);
    }

    /*
    L'historique des états d'une commande, du plus ancien au plus récent
*/
    public function findSuivi(string $numero): array
    {
        $query = $this->pdo->prepare('SELECT statut, date_heure, motif, mode_contact
                                  FROM suivi_commande
                                  WHERE numero_commande = :numero
                                  ORDER BY date_heure ASC, suivi_id ASC');
        $query->execute(['numero' => $numero]);
        return $query->fetchAll($this->pdo::FETCH_ASSOC);
    }

    /*
    Annulation par le client : seulement si la commande est encore en attente
*/
    public function annuler(string $numero, int $utilisateurId): void
    {
        try {
            $this->pdo->beginTransaction();

            // 1. On passe la commande à « annulée », seulement si elle est encore en attente
            $query = $this->pdo->prepare('UPDATE commande SET statut = \'annulée\'
                                      WHERE numero_commande = :numero
                                      AND utilisateur_id = :utilisateur_id
                                      AND statut = \'en attente\'');
            $query->execute(['numero' => $numero, 'utilisateur_id' => $utilisateurId]);
            if ($query->rowCount() === 0) {
                throw new \Exception('Cette commande ne peut plus être annulée.');
            }

            // 2. On ajoute l'état dans l'historique
            $query = $this->pdo->prepare('INSERT INTO suivi_commande (statut, date_heure, motif, numero_commande)
                                      VALUES (\'annulée\', NOW(), \'Annulée par le client\', :numero)');
            $query->execute(['numero' => $numero]);

            // 3. On remet le menu en stock
            $query = $this->pdo->prepare('UPDATE menu SET quantite_restante = quantite_restante + 1
                                      WHERE menu_id = (SELECT menu_id FROM commande WHERE numero_commande = :numero)');
            $query->execute(['numero' => $numero]);

            $this->pdo->commit();
        } catch (\Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
    /*
    Modification d'une commande encore en attente, dans une transaction
*/
    public function modifier(string $numero, int $utilisateurId, array $commande): void
    {
        try {
            $this->pdo->beginTransaction();

            // 1. On met à jour la commande, seulement si elle est encore en attente
            $query = $this->pdo->prepare('UPDATE commande SET
                date_prestation = :date_prestation, heure_livraison = :heure_livraison,
                adresse_livraison = :adresse_livraison, ville_livraison = :ville_livraison,
                distance_km = :distance_km, prix_menu = :prix_menu,
                nombre_personne = :nombre_personne, prix_livraison = :prix_livraison
            WHERE numero_commande = :numero AND utilisateur_id = :utilisateur_id AND statut = \'en attente\'');
            $query->execute([
                'date_prestation' => $commande['date_prestation'],
                'heure_livraison' => $commande['heure_livraison'],
                'adresse_livraison' => $commande['adresse_livraison'],
                'ville_livraison' => $commande['ville_livraison'],
                'distance_km' => $commande['distance_km'],
                'prix_menu' => $commande['prix_menu'],
                'nombre_personne' => $commande['nombre_personne'],
                'prix_livraison' => $commande['prix_livraison'],
                'numero' => $numero,
                'utilisateur_id' => $utilisateurId,
            ]);

            // 2. On remplace les plats choisis
            $query = $this->pdo->prepare('DELETE FROM commande_plat WHERE numero_commande = :numero');
            $query->execute(['numero' => $numero]);

            $query = $this->pdo->prepare('INSERT INTO commande_plat (numero_commande, plat_id, quantite)
                                      VALUES (:numero, :plat_id, :quantite)');
            foreach ($commande['lignes'] as $ligne) {
                $query->execute([
                    'numero' => $numero,
                    'plat_id' => $ligne['plat_id'],
                    'quantite' => $ligne['quantite'],
                ]);
            }

            // 3. On garde une trace dans l'historique
            $query = $this->pdo->prepare('INSERT INTO suivi_commande (statut, date_heure, motif, numero_commande)
                                      VALUES (\'en attente\', NOW(), \'Commande modifiée par le client\', :numero)');
            $query->execute(['numero' => $numero]);

            $this->pdo->commit();
        } catch (\Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    /*
    Toutes les commandes, filtrables par statut et par client
*/
    public function findAllForEmploye(?string $statut, ?string $client): array
    {
        $conditions = [];
        $params = [];

        if (!empty($statut)) {
            $conditions[] = 'c.statut = :statut';
            $params['statut'] = $statut;
        }
        if (!empty($client)) {
            $conditions[] = '(u.nom LIKE :client1 OR u.prenom LIKE :client2 OR u.email LIKE :client3
                              OR CONCAT(u.prenom, \' \', u.nom) LIKE :client4
                              OR CONCAT(u.nom, \' \', u.prenom) LIKE :client5)';
            for ($i = 1; $i <= 5; $i++) {
                $params['client' . $i] = '%' . $client . '%';
            }
        }

        $sql = 'SELECT c.numero_commande, c.date_prestation, c.statut, c.prix_menu, c.prix_livraison,
                   m.titre AS menu_titre, u.nom, u.prenom, u.email
            FROM commande c
            JOIN menu m ON m.menu_id = c.menu_id
            JOIN utilisateur u ON u.utilisateur_id = c.utilisateur_id';

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY c.date_prestation ASC';

        $query = $this->pdo->prepare($sql);
        $query->execute($params);
        return $query->fetchAll($this->pdo::FETCH_ASSOC);
    }

    /*
    Une commande avec les coordonnées du client (espace employé)
*/
    public function findOneByNumeroForEmploye(string $numero): array|bool
    {
        $query = $this->pdo->prepare('SELECT c.*, m.titre AS menu_titre, u.nom, u.prenom, u.email, u.telephone
                                  FROM commande c
                                  JOIN menu m ON m.menu_id = c.menu_id
                                  JOIN utilisateur u ON u.utilisateur_id = c.utilisateur_id
                                  WHERE c.numero_commande = :numero');
        $query->execute(['numero' => $numero]);
        return $query->fetch($this->pdo::FETCH_ASSOC);
    }

    /*
    Changement de statut par un employé, dans une transaction
*/
    public function changerStatut(string $numero, string $statut, ?string $motif, ?string $modeContact): void
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Mise à jour de la commande, sauf si elle est déjà terminée ou annulée
            $sql = 'UPDATE commande SET statut = :statut';
            if ($statut === 'en attente du retour de matériel') {
                $sql .= ', pret_materiel = 1';
            }
            if ($statut === 'terminée') {
                $sql .= ', restitution_materiel = pret_materiel';
            }
            $sql .= ' WHERE numero_commande = :numero AND statut NOT IN (\'terminée\', \'annulée\')';

            $query = $this->pdo->prepare($sql);
            $query->execute(['statut' => $statut, 'numero' => $numero]);
            if ($query->rowCount() === 0) {
                throw new \Exception('Le statut de cette commande ne peut plus être modifié.');
            }

            // 2. Ajout dans l'historique
            $query = $this->pdo->prepare('INSERT INTO suivi_commande (statut, date_heure, motif, mode_contact, numero_commande)
                                      VALUES (:statut, NOW(), :motif, :mode_contact, :numero)');
            $query->execute([
                'statut' => $statut,
                'motif' => $motif,
                'mode_contact' => $modeContact,
                'numero' => $numero,
            ]);

            // 3. En cas d'annulation, on remet le menu en stock
            if ($statut === 'annulée') {
                $query = $this->pdo->prepare('UPDATE menu SET quantite_restante = quantite_restante + 1
                                          WHERE menu_id = (SELECT menu_id FROM commande WHERE numero_commande = :numero)');
                $query->execute(['numero' => $numero]);
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
