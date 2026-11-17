<?php

namespace App\Repository;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;
use MongoDB\BSON\UTCDateTime;

class StatistiqueRepository
{
    private string $base;
    const COLLECTION = 'commandes_stats';

    private Manager $manager;

    public function __construct()
    {
    $config = require _ROOTPATH_ . '/App/Config/mongo_config.php';
    $this->manager = new Manager($config['uri']);
    $this->base = $config['base'];
    }

    /*
        Crée ou met à jour le document d'une commande (upsert)
    */
    public function enregistrerCommande(string $numero, int $menuId, string $menuTitre, float $chiffreAffaires, string $statut, ?string $dateCommande = null): void
    {
        $bulk = new BulkWrite();
        $bulk->update(
            ['numero_commande' => $numero],
            [
                '$set' => [
                    'menu_id' => $menuId,
                    'menu_titre' => $menuTitre,
                    'chiffre_affaires' => $chiffreAffaires,
                    'statut' => $statut,
                ],
                // La date n'est écrite qu'à la création du document
                '$setOnInsert' => [
                    'date_commande' => new UTCDateTime($dateCommande ? strtotime($dateCommande) * 1000 : null),
                ],
            ],
            ['upsert' => true]
        );
        $this->manager->executeBulkWrite($this->base . '.' . self::COLLECTION, $bulk);    }

    /*
        Met à jour seulement le statut d'une commande
    */
    public function changerStatut(string $numero, string $statut): void
    {
        $bulk = new BulkWrite();
        $bulk->update(
            ['numero_commande' => $numero],
            ['$set' => ['statut' => $statut]]
        );
        $this->manager->executeBulkWrite($this->base . '.' . self::COLLECTION, $bulk);
    }

    /*
        Nombre de commandes et chiffre d'affaires par menu (hors commandes annulées),
        avec des filtres facultatifs par menu et par période
    */
    public function statistiquesParMenu(?int $menuId, ?string $debut, ?string $fin): array
    {
        $filtre = ['statut' => ['$ne' => 'annulée']];

        if ($menuId) {
            $filtre['menu_id'] = $menuId;
        }
        if ($debut) {
            $filtre['date_commande']['$gte'] = new UTCDateTime(strtotime($debut . ' 00:00:00') * 1000);
        }
        if ($fin) {
            $filtre['date_commande']['$lte'] = new UTCDateTime(strtotime($fin . ' 23:59:59') * 1000);
        }

        $command = new Command([
            'aggregate' => self::COLLECTION,
            'pipeline' => [
                ['$match' => $filtre],
                ['$group' => [
                    '_id' => '$menu_titre',
                    'nb_commandes' => ['$sum' => 1],
                    'chiffre_affaires' => ['$sum' => '$chiffre_affaires'],
                ]],
                ['$sort' => ['nb_commandes' => -1]],
            ],
            'cursor' => new \stdClass(),
        ]);

        $resultats = [];
        foreach ($this->manager->executeCommand($this->base, $command) as $ligne) {
            $resultats[] = [
                'menu' => $ligne->_id,
                'nb_commandes' => $ligne->nb_commandes,
                'chiffre_affaires' => round($ligne->chiffre_affaires, 2),
            ];
        }
        return $resultats;
    }
}