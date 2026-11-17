<?php

namespace App\Entity;

class LigneCommande extends Entity{
    protected  ?string $numero_commande = null;
    protected ?int $plat_id = null;
    protected ?int $quantite = null;

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
     * Get the value of plat_id
     */
    public function getPlatId(): ?int
    {
        return $this->plat_id;
    }

    /**
     * Set the value of plat_id
     */
    public function setPlatId(?int $plat_id): self
    {
        $this->plat_id = $plat_id;

        return $this;
    }

    /**
     * Get the value of quantite
     */
    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    /**
     * Set the value of quantite
     */
    public function setQuantite(?int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }
}