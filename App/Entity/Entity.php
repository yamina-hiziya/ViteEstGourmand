<?php

namespace App\Entity;

use App\Tools\StringTools;


class Entity
{

    public static function createAndHydrate(array $data): static
    {
        // Ici static fait référence à la classe de l'enfant, alors que self fait référence à la classe courante
        $entity = new static();
        $entity->hydrate($data);
        return $entity;
    }

    public function hydrate(array $data)
    {
        // Pour chaque donnée reçue, on appelle le setter correspondant s'il existe
        foreach ($data as $key => $value) {
            $methodName = 'set' . StringTools::toPascalCase($key);
            if (method_exists($this, $methodName)) {
                $this->{$methodName}($value);
            }
        }
    }
}
