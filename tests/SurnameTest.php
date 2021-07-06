<?php

namespace App\Tests;

use App\Entity\Surname;
use App\Service\SlugGenerator;
use PHPUnit\Framework\TestCase;

class SurnameTest extends TestCase
{
    public function testSetValue(): void
    {
        $slugGenerator = new SlugGenerator();
        $surname = new Surname($slugGenerator);
        $surname->setLabel("Dupont");
        $surname->setWikidata("Q18627472");
        $surname->setLanguage("fr");

        $this->assertEquals("Dupont", $surname->getLabel());
        $this->assertEquals("fr", $surname->getLanguage());
        $this->assertEquals("DNOPTU", $surname->getLabels());
        $this->assertEquals(6, $surname->getLabelsLength());
        $this->assertEquals("Q18627472", $surname->getWikidata());

        //$this->assertEquals($surname->getLabels());
    }
}
