<?php

namespace App\Entity;

class Regime extends Entity
{
    protected ?int $regime_id = null;
    protected ?string $libelle = null;

    /**
     * Get the value of regime
     */
    public function getRegimeId(): ?int
    {
        return $this->regime_id;
    }

    /**
     * Set the value of regime
     */
    public function setRegimeId(?int $regime_id): self
    {
        $this->regime_id = $regime_id;

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
}