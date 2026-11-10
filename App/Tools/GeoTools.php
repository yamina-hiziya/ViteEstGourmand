<?php

namespace App\Tools;

class GeoTools{

    //coordonnées du centre de bordeaux point de départ de la livraison
    const BORDEAUX_LAT = 44.837789;
    const BORDEAUX_LON = -0.57918;

    /*
        Envoie une adresse à l'API de géocodage de l'IGN.
        Renvoie ['lat' => ..., 'lon' => ..., 'ville' => ...], ou null si l'adresse est introuvable.
    */
    public static function geocoder(string $adresse): ?array
    {
        $url = 'https://data.geopf.fr/geocodage/search?limit=1&q=' . urlencode($adresse);

        // On attend 5 secondes au maximum, pour ne pas bloquer la page
        $contexte = stream_context_create(['http' => ['timeout' => 5]]);
        $reponse = @file_get_contents($url, false, $contexte);

        if ($reponse === false) {
            return null;
        }

        $donnees = json_decode($reponse, true);

        if (empty($donnees['features'])) {
            return null;
        }

        $resultat = $donnees['features'][0];

        return [
            'lon' => $resultat['geometry']['coordinates'][0],
            'lat' => $resultat['geometry']['coordinates'][1],
            'ville' => $resultat['properties']['city'] ?? '',
        ];
    }

    /*
        Calcule la distance à vol d'oiseau entre deux points, en kilomètres (formule de Haversine)
    */
    public static function distanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $rayonTerre = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return $rayonTerre * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
