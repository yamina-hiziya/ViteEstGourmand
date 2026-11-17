<?php

namespace App\Repository;
use App\Entity\Theme;

class ThemeRepository extends Repository
{
    public function findAll(): array
    {
        $query = $this->pdo->prepare("SELECT * FROM theme ORDER BY libelle");
        $query->execute();
        $themes = $query->fetchAll($this->pdo::FETCH_ASSOC);

        $themesArray = [];

        if ($themes) {
            foreach ($themes as $theme) {
                $themesArray[] = Theme::createAndHydrate($theme);
            }
        }
        return $themesArray;
    }
}