<?php

namespace App\Entity;


class Role extends Entity
{

    protected ?int $role_id = null;
    protected ?string $libelle = null;
    
    /**
     * Get the value of id
     */
    public function getRoleId(): ?int
    {
        return $this->role_id;
    }
    
    /**
     * Set the value of id
     */
    public function setRoleId(?int $role_id): self
    {
        $this->role_id = $role_id;
        
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