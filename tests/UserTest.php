<?php

use PHPUnit\Framework\TestCase;
use App\Entity\User;

class UserTest extends TestCase
{
    public function testMotDePasseValideAccepte(): void
    {
        $this->assertNull(User::verifierFormatMotDePasse('Gourmand2026!'));
    }

    public function testMotDePasseTropCourtRefuse(): void
    {
        $this->assertNotNull(User::verifierFormatMotDePasse('Court1!'));
    }

    public function testMotDePasseSansMajusculeRefuse(): void
    {
        $this->assertNotNull(User::verifierFormatMotDePasse('gourmand2026!'));
    }

    public function testMotDePasseSansChiffreRefuse(): void
    {
        $this->assertNotNull(User::verifierFormatMotDePasse('Gourmandise!'));
    }

    public function testMotDePasseSansCaractereSpecialRefuse(): void
    {
        $this->assertNotNull(User::verifierFormatMotDePasse('Gourmand2026'));
    }

    public function testMotDePasseVideRefuse(): void
    {
        $this->assertNotNull(User::verifierFormatMotDePasse(''));
    }
}