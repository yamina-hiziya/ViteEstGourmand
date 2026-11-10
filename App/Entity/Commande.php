<?php

namespace App\Entity;


class Commande extends Entity
{

    protected ?string $numero_commande = null;
    protected ?string $date_commande = null;
    protected ?string $date_prestation = null;
    protected ?string $heure_livraison = null;
    protected ?string $adresse_livraison = null;
    protected ?string $ville_livraison = null;
    protected ?float $distance_km = null;
    protected ?float $prix_menu = null;
    protected ?int $nombre_personnes = null;
    protected ?float $prix_livraison = null;
    protected ?string $statut = null;
    protected ?bool $pret_materiel = null;
    protected ?bool $restitution_materiel = null;
    protected ?int $utilisateur_id = null;
    protected ?int $menu_id = null;

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
     * Get the value of date_commande
     */
    public function getDateCommande(): ?string
    {
        return $this->date_commande;
    }

    /**
     * Set the value of date_commande
     */
    public function setDateCommande(?string $date_commande): self
    {
        $this->date_commande = $date_commande;

        return $this;
    }

    /**
     * Get the value of date_prestation
     */
    public function getDatePrestation(): ?string
    {
        return $this->date_prestation;
    }

    /**
     * Set the value of date_prestation
     */
    public function setDatePrestation(?string $date_prestation): self
    {
        $this->date_prestation = $date_prestation;

        return $this;
    }

    /**
     * Get the value of heure_livraison
     */
    public function getHeureLivraison(): ?string
    {
        return $this->heure_livraison;
    }

    /**
     * Set the value of heure_livraison
     */
    public function setHeureLivraison(?string $heure_livraison): self
    {
        $this->heure_livraison = $heure_livraison;

        return $this;
    }

    /**
     * Get the value of adresse_livraison
     */
    public function getAdresseLivraison(): ?string
    {
        return $this->adresse_livraison;
    }

    /**
     * Set the value of adresse_livraison
     */
    public function setAdresseLivraison(?string $adresse_livraison): self
    {
        $this->adresse_livraison = $adresse_livraison;

        return $this;
    }

    /**
     * Get the value of ville_livraison
     */
    public function getVilleLivraison(): ?string
    {
        return $this->ville_livraison;
    }

    /**
     * Set the value of ville_livraison
     */
    public function setVilleLivraison(?string $ville_livraison): self
    {
        $this->ville_livraison = $ville_livraison;

        return $this;
    }

    /**
     * Get the value of distance_km
     */
    public function getDistanceKm(): ?float
    {
        return $this->distance_km;
    }

    /**
     * Set the value of distance_km
     */
    public function setDistanceKm(?float $distance_km): self
    {
        $this->distance_km = $distance_km;

        return $this;
    }

    /**
     * Get the value of prix_menu
     */
    public function getPrixMenu(): ?float
    {
        return $this->prix_menu;
    }

    /**
     * Set the value of prix_menu
     */
    public function setPrixMenu(?float $prix_menu): self
    {
        $this->prix_menu = $prix_menu;

        return $this;
    }

    /**
     * Get the value of nombre_personnes
     */
    public function getNombrePersonnes(): ?int
    {
        return $this->nombre_personnes;
    }

    /**
     * Set the value of nombre_personnes
     */
    public function setNombrePersonnes(?int $nombre_personnes): self
    {
        $this->nombre_personnes = $nombre_personnes;

        return $this;
    }

    /**
     * Get the value of prix_livraison
     */
    public function getPrixLivraison(): ?float
    {
        return $this->prix_livraison;
    }

    /**
     * Set the value of prix_livraison
     */
    public function setPrixLivraison(?float $prix_livraison): self
    {
        $this->prix_livraison = $prix_livraison;

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
     * Get the value of pret_materiel
     */
    public function isPretMateriel(): ?bool
    {
        return $this->pret_materiel;
    }

    /**
     * Set the value of pret_materiel
     */
    public function setPretMateriel(?bool $pret_materiel): self
    {
        $this->pret_materiel = $pret_materiel;

        return $this;
    }

    /**
     * Get the value of restitution_materiel
     */
    public function isRestitutionMateriel(): ?bool
    {
        return $this->restitution_materiel;
    }

    /**
     * Set the value of restitution_materiel
     */
    public function setRestitutionMateriel(?bool $restitution_materiel): self
    {
        $this->restitution_materiel = $restitution_materiel;

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
    // les calculs metier devrait aller dans une classe de service, mais pour l'instant on les met dans l'entité Commande 

    /*
    Prix du menu : prix par personne × nombre de personnes,
    avec 10 % de réduction à partir de 5 personnes de plus que le minimum du menu
*/
    public static function calculerPrixMenu(float $prixParPersonne, int $nbPersonnes, int $minimum): float
    {
        $prix = $prixParPersonne * $nbPersonnes;

        if ($nbPersonnes >= $minimum + 5) {
            $prix = $prix * 0.9;
        }

        return round($prix, 2);
    }

    /*
    Frais de livraison : gratuits à Bordeaux, sinon 5 € + 0,59 € par kilomètre
*/
    public static function calculerFraisLivraison(string $ville, float $distanceKm): float
    {
        if (mb_strtolower(trim($ville)) === 'bordeaux') {
            return 0.0;
        }

        return round(5 + 0.59 * $distanceKm, 2);
    }
}
