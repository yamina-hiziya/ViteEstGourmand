<?php

namespace App\Entity;

class Plat extends Entity
{
    protected ?int $plat_id = null;
    protected ?string $titre_plat = null;
    protected ?string $photo = null;
    protected ?string $type_plat = null;
    protected ?string $allergenes = null;
    

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
     * Get the value of titre_plat
     */
    public function getTitrePlat(): ?string
    {
        return $this->titre_plat;
    }

    /**
     * Set the value of titre_plat
     */
    public function setTitrePlat(?string $titre_plat): self
    {
        $this->titre_plat = $titre_plat;

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
     * Get the value of type_plat
     */
    public function getTypePlat(): ?string
    {
        return $this->type_plat;
    }

    /**
     * Set the value of type_plat
     */
    public function setTypePlat(?string $type_plat): self
    {
        $this->type_plat = $type_plat;

        return $this;
    }

    /**
     * Get the value of allergenes
     */
    public function getAllergenes(): ?string
    {
        return $this->allergenes;
    }

    /**
     * Set the value of allergenes
     */
    public function setAllergenes(?string $allergenes): self
    {
        $this->allergenes = $allergenes;

        return $this;
    }
}