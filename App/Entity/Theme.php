<?php

namespace App\Entity;

class Theme extends Entity
{
    protected ?int $theme_id = null;
    protected ?string $libelle = null;

    /**
     * Get the value of theme
     */
    public function getThemeId(): ?int
    {
        return $this->theme_id;
    }

    /**
     * Set the value of theme
     */
    public function setThemeId(?int $theme_id): self
    {
        $this->theme_id = $theme_id;

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