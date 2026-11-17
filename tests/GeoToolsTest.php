<?php

use PHPUnit\Framework\TestCase;
use App\Tools\GeoTools;

class GeoToolsTest extends TestCase
{
    public function testDistanceNullePourUnMemePoint(): void
    {
        $distance = GeoTools::distanceKm(GeoTools::BORDEAUX_LAT, GeoTools::BORDEAUX_LON, GeoTools::BORDEAUX_LAT, GeoTools::BORDEAUX_LON);
        $this->assertEqualsWithDelta(0, $distance, 0.001);
    }

    public function testDistanceBordeauxMontpellier(): void
    {
        // Environ 380 km à vol d'oiseau
        $distance = GeoTools::distanceKm(GeoTools::BORDEAUX_LAT, GeoTools::BORDEAUX_LON, 43.608525, 3.879638);
        $this->assertEqualsWithDelta(380.6, $distance, 1);
    }
}