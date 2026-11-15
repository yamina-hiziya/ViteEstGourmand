<?php

namespace App\Repository;

use App\Entity\User;

class UserRepository extends Repository
{
    public function findOneById(int $id): User|bool
    {
        $query = $this->pdo->prepare("SELECT * FROM utilisateur WHERE utilisateur_id = :id");
        $query->bindValue(':id', $id, $this->pdo::PARAM_INT);
        $query->execute();
        $user = $query->fetch($this->pdo::FETCH_ASSOC);
        if ($user) {
            return User::createAndHydrate($user);
        } else {
            return false;
        }
    }

    public function findOneByEmail(string $email): User|bool
    {
        $query = $this->pdo->prepare("SELECT * FROM utilisateur WHERE email = :email");
        $query->bindValue(':email', $email, $this->pdo::PARAM_STR);
        $query->execute();
        $user = $query->fetch($this->pdo::FETCH_ASSOC);
        if ($user) {
            return User::createAndHydrate($user);
        } else {
            return false;
        }
    }

    public function persist(User $user): bool
    {
        if ($user->getUtilisateurId() !== null) {
            // Modification : on ne touche pas au mot de passe
            $query = $this->pdo->prepare('UPDATE utilisateur
                                        SET nom = :nom, prenom = :prenom, email = :email,
                                            telephone = :telephone, adresse_postale = :adresse_postale,
                                            ville = :ville, pays = :pays
                                        WHERE utilisateur_id = :id');
            $query->bindValue(':id', $user->getUtilisateurId(), $this->pdo::PARAM_INT);
        } else {
            // Création : on enregistre aussi le mot de passe haché, le rôle et le statut actif
            $query = $this->pdo->prepare('INSERT INTO utilisateur
                                        (nom, prenom, email, password, telephone, adresse_postale, ville, pays, role_id, actif)
                                        VALUES (:nom, :prenom, :email, :password, :telephone, :adresse_postale, :ville, :pays, :role_id, :actif)');
            $query->bindValue(':password', password_hash($user->getPassword(), PASSWORD_DEFAULT), $this->pdo::PARAM_STR);
            $query->bindValue(':role_id', $user->getRoleId(), $this->pdo::PARAM_INT);
            $query->bindValue(':actif', 1, $this->pdo::PARAM_INT);
        }

        $query->bindValue(':nom', $user->getNom(), $this->pdo::PARAM_STR);
        $query->bindValue(':prenom', $user->getPrenom(), $this->pdo::PARAM_STR);
        $query->bindValue(':email', $user->getEmail(), $this->pdo::PARAM_STR);
        $query->bindValue(':telephone', $user->getTelephone(), $this->pdo::PARAM_STR);
        $query->bindValue(':adresse_postale', $user->getAdressePostale(), $this->pdo::PARAM_STR);
        $query->bindValue(':ville', $user->getVille(), $this->pdo::PARAM_STR);
        $query->bindValue(':pays', $user->getPays(), $this->pdo::PARAM_STR);

        return $query->execute();
    }

    /*
    Tous les comptes employés (role_id = 2)
*/
    public function findAllEmployes(): array
    {
        $query = $this->pdo->prepare('SELECT * FROM utilisateur WHERE role_id = 2 ORDER BY nom, prenom');
        $query->execute();
        $employes = [];
        foreach ($query->fetchAll($this->pdo::FETCH_ASSOC) as $employe) {
            $employes[] = User::createAndHydrate($employe);
        }
        return $employes;
    }

    /*
    Active ou désactive un compte, seulement s'il s'agit d'un employé
*/
    public function changerActif(int $utilisateurId, bool $actif): bool
    {
        $query = $this->pdo->prepare('UPDATE utilisateur SET actif = :actif
                                  WHERE utilisateur_id = :id AND role_id = 2');
        $query->execute(['actif' => $actif ? 1 : 0, 'id' => $utilisateurId]);
        return $query->rowCount() > 0;
    }
}
