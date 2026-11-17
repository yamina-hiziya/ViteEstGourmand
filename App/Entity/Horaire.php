<?php

namespace App\Entity;

class Horaire extends Entity
{
    protected ?int $horaire_id = null;
    protected ?string $jour = null;
    protected ?string $heure_ouverture = null;
    protected ?string $heure_fermeture = null;

    

    /**
     * Get the value of horaire_id
     */
    public function getHoraireId(): ?int
    {
        return $this->horaire_id;
    }

    /**
     * Set the value of horaire_id
     */
    public function setHoraireId(?int $horaire_id): self
    {
        $this->horaire_id = $horaire_id;

        return $this;
    }

    /**
     * Get the value of jour
     */
    public function getJour(): ?string
    {
        return $this->jour;
    }

    /**
     * Set the value of jour
     */
    public function setJour(?string $jour): self
    {
        $this->jour = $jour;

        return $this;
    }

    /**
     * Get the value of heure_ouverture
     */
    public function getHeureOuverture(): ?string
    {
        return $this->heure_ouverture;
    }

    /**
     * Set the value of heure_ouverture
     */
    public function setHeureOuverture(?string $heure_ouverture): self
    {
        $this->heure_ouverture = $heure_ouverture;

        return $this;
    }

    /**
     * Get the value of heure_fermeture
     */
    public function getHeureFermeture(): ?string
    {
        return $this->heure_fermeture;
    }

    /**
     * Set the value of heure_fermeture
     */
    public function setHeureFermeture(?string $heure_fermeture): self
    {
        $this->heure_fermeture = $heure_fermeture;

        return $this;
    }
}