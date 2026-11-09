<?php

namespace App\Entity;


class User extends Entity
{
    protected ?int $utilisateur_id = null;
    protected ?string $email = null;
    protected ?string $password = null;
    protected ?string $nom = null;
    protected ?string $prenom = null;
    protected ?string $telephone = null;
    protected ?string $ville = null;
    protected ?string $pays = null;
    protected ?string $adresse_postale = null;
    protected ?bool $actif = null;
    protected ?int $role_id = null;
    
    /**
     * Get the value of utilisateur_id
     */
    public function getUtilisateurId(): ?int
    {
        return $this->utilisateur_id;
    }

    /**
     * Set the value of utilisateur_id
     */
    public function setUtilisateurId(?int $utilisateur_id): self
    {
        $this->utilisateur_id = $utilisateur_id;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set the value of password
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of nom
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Set the value of nom
     */
    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get the value of prenom
     */
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    /**
     * Set the value of prenom
     */
    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get the value of telephone
     */
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * Set the value of telephone
     */
    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get the value of ville
     */
    public function getVille(): ?string
    {
        return $this->ville;
    }

    /**
     * Set the value of ville
     */
    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get the value of pays
     */
    public function getPays(): ?string
    {
        return $this->pays;
    }

    /**
     * Set the value of pays
     */
    public function setPays(?string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get the value of adresse_postale
     */
    public function getAdressePostale(): ?string
    {
        return $this->adresse_postale;
    }

    /**
     * Set the value of adresse_postale
     */
    public function setAdressePostale(?string $adresse_postale): self
    {
        $this->adresse_postale = $adresse_postale;

        return $this;
    }

    /**
     * Get the value of actif
     */
    public function isActif(): ?bool
    {
        return $this->actif;
    }

    /**
     * Set the value of actif
     */
    public function setActif(?bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get the value of role_id
     */
    public function getRoleId(): ?int
    {
        return $this->role_id;
    }

    /**
     * Set the value of role_id
     */
    public function setRoleId(?int $role_id): self
    {
        $this->role_id = $role_id;

        return $this;
    }



    /*
        Pourrait être déplacé dans une classe UserValidator
    */
    public function validate(): array
{
    $errors = [];
    if (empty($this->getNom())) {
        $errors['nom'] = 'Le champ nom ne doit pas être vide';
    }
    if (empty($this->getPrenom())) {
        $errors['prenom'] = 'Le champ prénom ne doit pas être vide';
    }
    if (empty($this->getEmail())) {
        $errors['email'] = 'Le champ email ne doit pas être vide';
    } else if (!filter_var($this->getEmail(), FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'L\'email n\'est pas valide';
    }
    if (empty($this->getTelephone())) {
        $errors['telephone'] = 'Le champ téléphone ne doit pas être vide';
    }
    if (empty($this->getAdressePostale())) {
        $errors['adresse_postale'] = 'Le champ adresse ne doit pas être vide';
    }
    if (empty($this->getVille())) {
        $errors['ville'] = 'Le champ ville ne doit pas être vide';
    }
    if (empty($this->getPays())) {
        $errors['pays'] = 'Le champ pays ne doit pas être vide';
    }
    if (empty($this->getPassword())) {
        $errors['password'] = 'Le champ mot de passe ne doit pas être vide';
    } else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{10,}$/', $this->getPassword())) {
        $errors['password'] = 'Le mot de passe doit contenir au moins 10 caractères, dont une majuscule, une minuscule, un chiffre et un caractère spécial';
    }
    return $errors;
}

    /*
        Pourrait être déplacé dans une classe Security
    */
    public function verifyPassword(string $password): bool
    {
        if (password_verify($password, $this->password)) {
            return true;
        } else {
            return false;
        }
    }

    /*
        Pourrait être déplacé dans une classe Security
    */
    public static function isLogged(): bool
    {
        return isset($_SESSION['user']);
    }


    /*
        Pourrait être déplacé dans une classe Security
    */
    public static function isUser(): bool
{
    return isset($_SESSION['user']) && $_SESSION['user']['role_id'] === 1;
}

public static function isEmploye(): bool
{
    return isset($_SESSION['user']) && $_SESSION['user']['role_id'] === 2;
}

public static function isAdmin(): bool
{
    return isset($_SESSION['user']) && $_SESSION['user']['role_id'] === 3;
}

}
