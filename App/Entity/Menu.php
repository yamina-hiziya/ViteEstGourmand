<?php

namespace App\Entity;

class Menu extends Entity
{
    protected ?int $menu_id = null;
    protected ?string $titre = null;
    protected ?int $nombre_personne_minimum = null;
    protected ?float $prix_par_personne = null;
    protected ?string $description = null;
    protected ?int $quantite_restante = null;
    protected ?string $conditions = null;
    protected ?string $photo = null;
    protected ?int $theme_id = null;
    protected ?int $regime_id = null;
    protected ?string $theme_libelle = null;
    protected ?string $regime_libelle = null;
    




    /**
     * Get the value of menu_id
     */
    public function getMenuId(): ?int
    {
        return $this->menu_id;
    }

    /**
     * Set the value of menu_id
     */
    public function setMenuId(?int $menu_id): self
    {
        $this->menu_id = $menu_id;

        return $this;
    }

    /**
     * Get the value of titre
     */
    public function getTitre(): ?string
    {
        return $this->titre;
    }

    /**
     * Set the value of titre
     */
    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get the value of nombre_personne_minimum
     */
    public function getNombrePersonneMinimum(): ?int
    {
        return $this->nombre_personne_minimum;
    }

    /**
     * Set the value of nombre_personne_minimum
     */
    public function setNombrePersonneMinimum(?int $nombre_personne_minimum): self
    {
        $this->nombre_personne_minimum = $nombre_personne_minimum;

        return $this;
    }

    /**
     * Get the value of prix_par_personne
     */
    public function getPrixParPersonne(): ?float
    {
        return $this->prix_par_personne;
    }

    /**
     * Set the value of prix_par_personne
     */
    public function setPrixParPersonne(?float $prix_par_personne): self
    {
        $this->prix_par_personne = $prix_par_personne;

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
     * Get the value of quantite_restante
     */
    public function getQuantiteRestante(): ?int
    {
        return $this->quantite_restante;
    }

    /**
     * Set the value of quantite_restante
     */
    public function setQuantiteRestante(?int $quantite_restante): self
    {
        $this->quantite_restante = $quantite_restante;

        return $this;
    }

    /**
     * Get the value of conditions
     */
    public function getConditions(): ?string
    {
        return $this->conditions;
    }

    /**
     * Set the value of conditions
     */
    public function setConditions(?string $conditions): self
    {
        $this->conditions = $conditions;

        return $this;
    }

    /**
     * Get the value of photo
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * Set the value of photo
     */
    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get the value of theme_id
     */
    public function getThemeId(): ?int
    {
        return $this->theme_id;
    }

    /**
     * Set the value of theme_id
     */
    public function setThemeId(?int $theme_id): self
    {
        $this->theme_id = $theme_id;

        return $this;
    }

    /**
     * Get the value of regime_id
     */
    public function getRegimeId(): ?int
    {
        return $this->regime_id;
    }

    /**
     * Set the value of regime_id
     */
    public function setRegimeId(?int $regime_id): self
    {
        $this->regime_id = $regime_id;

        return $this;
    }

    /**
     * Get the value of theme_libelle
     */
    public function getThemeLibelle(): ?string
    {
        return $this->theme_libelle;
    }

    /**
     * Set the value of theme_libelle
     */
    public function setThemeLibelle(?string $theme_libelle): self
    {
        $this->theme_libelle = $theme_libelle;

        return $this;
    }

    /**
     * Get the value of regime_libelle
     */
    public function getRegimeLibelle(): ?string
    {
        return $this->regime_libelle;
    }

    /**
     * Set the value of regime_libelle
     */
    public function setRegimeLibelle(?string $regime_libelle): self
    {
        $this->regime_libelle = $regime_libelle;

        return $this;
    }
}