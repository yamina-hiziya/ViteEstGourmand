<?php

use PHPUnit\Framework\TestCase;
use App\Entity\Commande;

class CommandeTest extends TestCase
{
    public function testPrixSansReduction(): void
    {
        // 6 personnes à 30 € pour un minimum de 6 : pas de réduction
        $this->assertSame(180.0, (float) Commande::calculerPrixMenu(30, 6, 6));
    }

    public function testPasDeReductionJusteAvantLeSeuil(): void
    {
        // 10 personnes pour un minimum de 6 : il en faut 11 pour la réduction
        $this->assertSame(300.0, (float) Commande::calculerPrixMenu(30, 10, 6));
    }

    public function testReductionDe10PourcentAuSeuil(): void
    {
        // 11 personnes = minimum + 5 : 330 € moins 10 % = 297 €
        $this->assertSame(297.0, (float) Commande::calculerPrixMenu(30, 11, 6));
    }

    public function testLivraisonGratuiteABordeaux(): void
    {
        $this->assertEquals(0, Commande::calculerFraisLivraison('Bordeaux', 0));
        // Quelle que soit l'écriture de la ville
        $this->assertEquals(0, Commande::calculerFraisLivraison('  BORDEAUX ', 3));
    }

    public function testLivraisonHorsBordeaux(): void
    {
        // 5 € + 0,59 € × 10 km = 10,90 €
        $this->assertEquals(10.9, Commande::calculerFraisLivraison('Mérignac', 10));
    }
}