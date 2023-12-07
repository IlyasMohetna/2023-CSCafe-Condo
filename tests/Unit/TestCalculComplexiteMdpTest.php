<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Fonctions;

class TestCalculComplexiteMdpTest extends TestCase
{
    public function testCalculComplexiteMdp()
    {
        $this->assertEquals(38, Fonctions\CalculComplexiteMdp('MotDePasse1'));
        $this->assertEquals(33, Fonctions\CalculComplexiteMdp('MotDePasse'));
        $this->assertEquals(69, Fonctions\CalculComplexiteMdp('StrongPassword123'));
        $this->assertEquals(23, Fonctions\CalculComplexiteMdp('WeakPwd'));
        $this->assertEquals(58, Fonctions\CalculComplexiteMdp('SecurePass!2023'));
        $this->assertEquals(80, Fonctions\CalculComplexiteMdp('VerySecureP@ssw0rd!'));
        $this->assertEquals(26, Fonctions\CalculComplexiteMdp('ShortPwd'));
        $this->assertEquals(43, Fonctions\CalculComplexiteMdp('MediumPwd123'));
        $this->assertEquals(282, Fonctions\CalculComplexiteMdp('UltraSecureP@ssw0rd!123456789012345678901234567890'));
        $this->assertEquals(58, Fonctions\CalculComplexiteMdp('Alphanumeric123'));
        $this->assertEquals(58, Fonctions\CalculComplexiteMdp('SpecialChars!@#'));
        $this->assertEquals(43, Fonctions\CalculComplexiteMdp('MixChars123!'));
        $this->assertEquals(43, Fonctions\CalculComplexiteMdp('SimplePwd123'));
    }
}
