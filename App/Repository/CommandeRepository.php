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
}
