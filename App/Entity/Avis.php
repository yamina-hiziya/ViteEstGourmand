<?php

namespace App\Entity;

class Avis extends Entity
{
    protected ?int $avis_id = null;
    protected ?string $libelle = '';
    protected ?int $note = null;
    protected ?string $description = null;
    protected ?string $status = null; 
    protected ?string $numero_commande = null;
    protected ?int $utilisateur_id = null;

    /**
     * Get the value of avis
     */
    public function getAvisId(): ?int
    {
        return $this->avis_id;
    }
    /**
     * Set the value of avis
     */
    public function setAvisId(?int $avis_id): self
    {
        $this->avis_id = $avis_id;

        return $this;
    }
    /**
     * Get the value of libelle
     */
    public function getLibelle(): ?string
    {
        return $this->libelle;
    }
    /**
     * Set the value of libelle
     */
    public function setLibelle(?string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }
    /**
     * Get the value of note
     */
    public function getNote(): ?int
    {
        return $this->note;
    }
    /**
     * Set the value of note
     */
    public function setNote(?int $note): self
    {
        $this->note = $note;

        return $this;
    }
    /**
     * Get the value of description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
    /**
     * Get the value of status
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set the value of status
     */
    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of numero_commande
     */
    public function getNumeroCommande(): ?string
    {
        return $this->numero_commande;
    }

    /**
     * Set the value of numero_commande
     */
    public function setNumeroCommande(?string $numero_commande): self
    {
        $this->numero_commande = $numero_commande;

        return $this;
    }

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
}