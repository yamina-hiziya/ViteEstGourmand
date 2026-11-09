<?php

namespace App\Entity;

class SuiviCommande extends Entity 
{
    protected ?int $suivi_id = null;
    protected ?string $statut = null;
    protected ?string $date_heure = null;
    protected ?string $motif = null;
    protected ?string $mode_contact = null;
    protected ?string $numero_commande = null;
    

    /**
     * Get the value of suivi_id
     */
    public function getSuiviId(): ?int
    {
        return $this->suivi_id;
    }

    /**
     * Set the value of suivi_id
     */
    public function setSuiviId(?int $suivi_id): self
    {
        $this->suivi_id = $suivi_id;

        return $this;
    }

    /**
     * Get the value of statut
     */
    public function getStatut(): ?string
    {
        return $this->statut;
    }

    /**
     * Set the value of statut
     */
    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get the value of date_heure
     */
    public function getDateHeure(): ?string
    {
        return $this->date_heure;
    }

    /**
     * Set the value of date_heure
     */
    public function setDateHeure(?string $date_heure): self
    {
        $this->date_heure = $date_heure;

        return $this;
    }

    /**
     * Get the value of motif
     */
    public function getMotif(): ?string
    {
        return $this->motif;
    }

    /**
     * Set the value of motif
     */
    public function setMotif(?string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    /**
     * Get the value of mode_contact
     */
    public function getModeContact(): ?string
    {
        return $this->mode_contact;
    }

    /**
     * Set the value of mode_contact
     */
    public function setModeContact(?string $mode_contact): self
    {
        $this->mode_contact = $mode_contact;

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
}